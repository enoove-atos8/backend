<?php

namespace App\Domain\AI\Search\Actions;

use App\Domain\AI\Search\DataTransferObjects\AiSearchHistoryData;
use App\Domain\AI\Search\Exceptions\RateLimitExceededException;
use App\Domain\AI\Search\Interfaces\AiSearchHistoryRepositoryInterface;
use App\Domain\AI\Search\Interfaces\LlmServiceInterface;
use App\Domain\AI\Search\Services\SchemaExtractorService;
use App\Domain\AI\Search\Services\SqlValidatorService;
use Illuminate\Support\Facades\Log;

class ProcessSearchQueryAction
{
    public const ERROR_VALIDATION_FAILED = 'SQL gerado não passou na validação de segurança';

    public const ERROR_PROCESSING = 'Erro ao processar a consulta';

    private const FOLLOWUP_PATTERNS_PATH = 'prompts/ai_search_followup_patterns.txt';

    private const FOLLOWUP_CONTEXT_PATH = 'prompts/ai_search_followup_context.txt';

    private const PLACEHOLDER_PREVIOUS_QUESTION = '{{PREVIOUS_QUESTION}}';

    private const PLACEHOLDER_PREVIOUS_SQL = '{{PREVIOUS_SQL}}';

    private const PLACEHOLDER_PREVIOUS_DATA = '{{PREVIOUS_DATA}}';

    private const PLACEHOLDER_CURRENT_QUESTION = '{{CURRENT_QUESTION}}';

    private SchemaExtractorService $schemaExtractor;

    private LlmServiceInterface $llmService;

    private SqlValidatorService $sqlValidator;

    private AiSearchHistoryRepositoryInterface $historyRepository;

    public function __construct(
        SchemaExtractorService $schemaExtractor,
        LlmServiceInterface $llmService,
        SqlValidatorService $sqlValidator,
        AiSearchHistoryRepositoryInterface $historyRepository
    ) {
        $this->schemaExtractor = $schemaExtractor;
        $this->llmService = $llmService;
        $this->sqlValidator = $sqlValidator;
        $this->historyRepository = $historyRepository;
    }

    public function execute(AiSearchHistoryData $searchData): AiSearchHistoryData
    {
        $startTime = microtime(true);

        try {
            $schema = $this->schemaExtractor->extract();

            $questionWithContext = $this->addContextIfFollowup($searchData);

            $sqlGenerated = $this->llmService->generateSql($questionWithContext, $schema);

            $validation = $this->sqlValidator->validate($sqlGenerated);
            if (! $validation['valid']) {
                $searchData->sqlGenerated = $sqlGenerated;
                $searchData->executionTimeMs = $this->calculateExecutionTime($startTime);
                $searchData->success = false;
                $searchData->errorMessage = self::ERROR_VALIDATION_FAILED.': '.$validation['reason'];
                $searchData->rateLimitExceeded = false;
                $searchData->llmProvider = $this->llmService->getProviderName();

                return $this->historyRepository->save($searchData);
            }

            $data = $this->historyRepository->executeQuery($sqlGenerated);

            $formattedResponse = $this->llmService->formatResponse($searchData->question, $data);

            $searchData->sqlGenerated = $sqlGenerated;
            $searchData->resultData = $data;
            $searchData->resultTitle = $formattedResponse['title'];
            $searchData->resultDescription = $formattedResponse['description'];
            $searchData->suggestedFollowup = $formattedResponse['suggested_followup'];
            $searchData->executionTimeMs = $this->calculateExecutionTime($startTime);
            $searchData->success = true;
            $searchData->rateLimitExceeded = false;
            $searchData->llmProvider = $this->llmService->getProviderName();

            return $this->historyRepository->save($searchData);
        } catch (RateLimitExceededException $e) {
            Log::warning('AI Search rate limit exceeded', [
                'question' => $searchData->question,
            ]);

            $searchData->executionTimeMs = $this->calculateExecutionTime($startTime);
            $searchData->success = false;
            $searchData->errorMessage = $e->getMessage();
            $searchData->rateLimitExceeded = true;

            return $this->historyRepository->save($searchData);
        } catch (\Exception $e) {
            Log::error('AI Search error', [
                'question' => $searchData->question,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $searchData->executionTimeMs = $this->calculateExecutionTime($startTime);
            $searchData->success = false;
            $searchData->errorMessage = self::ERROR_PROCESSING.': '.$e->getMessage();
            $searchData->rateLimitExceeded = false;

            return $this->historyRepository->save($searchData);
        }
    }

    private function calculateExecutionTime(float $startTime): int
    {
        return (int) round((microtime(true) - $startTime) * 1000);
    }

    /**
     * @return array<string>
     */
    private function loadFollowupPatterns(): array
    {
        $path = resource_path(self::FOLLOWUP_PATTERNS_PATH);

        if (! file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);
        $lines = explode("\n", $content);

        return array_filter(array_map('trim', $lines), fn ($line) => ! empty($line) && ! str_starts_with($line, '#'));
    }

    private function loadFollowupContextTemplate(): string
    {
        $path = resource_path(self::FOLLOWUP_CONTEXT_PATH);

        if (! file_exists($path)) {
            return '{{CURRENT_QUESTION}}';
        }

        return file_get_contents($path);
    }

    private function isFollowupQuestion(string $question): bool
    {
        $patterns = $this->loadFollowupPatterns();

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $question)) {
                return true;
            }
        }

        return false;
    }

    private function addContextIfFollowup(AiSearchHistoryData $searchData): string
    {
        if (! $this->isFollowupQuestion($searchData->question)) {
            return $searchData->question;
        }

        $lastSearch = $this->historyRepository->getLastSuccessfulByUserId($searchData->userId);

        if (! $lastSearch || empty($lastSearch->resultData)) {
            return $searchData->question;
        }

        $contextData = is_array($lastSearch->resultData)
            ? $lastSearch->resultData
            : json_decode($lastSearch->resultData, true);

        if (empty($contextData)) {
            return $searchData->question;
        }

        $template = $this->loadFollowupContextTemplate();
        $context = str_replace(self::PLACEHOLDER_PREVIOUS_QUESTION, $lastSearch->question, $template);
        $context = str_replace(self::PLACEHOLDER_PREVIOUS_SQL, $lastSearch->sqlGenerated ?? '', $context);
        $context = str_replace(self::PLACEHOLDER_PREVIOUS_DATA, json_encode($contextData), $context);
        $context = str_replace(self::PLACEHOLDER_CURRENT_QUESTION, $searchData->question, $context);

        Log::info('AI Search followup detected', [
            'original_question' => $searchData->question,
            'previous_question' => $lastSearch->question,
        ]);

        return $context;
    }
}
