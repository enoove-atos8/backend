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

            $sqlGenerated = $this->llmService->generateSql($searchData->question, $schema);

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
}
