# Padrões de Projeto - ATOS8

## 📋 Índice

- [Visão Geral da Arquitetura](#visão-geral-da-arquitetura)
- [Estrutura de Diretórios](#estrutura-de-diretórios)
- [Padrões de Implementação](#padrões-de-implementação)
  - [1. Repository Pattern](#1-repository-pattern)
  - [2. Data Transfer Objects (DTOs)](#2-data-transfer-objects-dtos)
  - [3. Actions Pattern](#3-actions-pattern)
  - [4. Services Pattern](#4-services-pattern)
  - [5. Controllers](#5-controllers)
  - [6. Form Requests](#6-form-requests)
- [Fluxo de uma Requisição](#fluxo-de-uma-requisição)
- [Convenções e Boas Práticas](#convenções-e-boas-práticas)

---

## Visão Geral da Arquitetura

O projeto ATOS8 utiliza uma arquitetura **Domain-Driven Design (DDD)** com Laravel 11, seguindo princípios de Clean Architecture e separação clara de responsabilidades.

### Stack Tecnológica

- **Framework**: Laravel 11 (PHP 8.2+)
- **Arquitetura**: DDD + Clean Architecture
- **Multi-tenancy**: Stancl Tenancy
- **DTOs**: Spatie Data Transfer Object
- **Storage**: AWS S3 / MinIO

---

## Estrutura de Diretórios

```
app/
├── Application/          # Camada de Aplicação
│   ├── Api/             # Controllers da API (versionados)
│   │   └── v1/          # Versão 1 da API
│   ├── Core/            # Funcionalidades core da aplicação
│   │   ├── Http/        # Controllers base, middleware
│   │   ├── Jobs/        # Jobs assíncronos
│   │   ├── Console/     # Comandos Artisan
│   │   └── Helpers/     # Funções auxiliares
│
├── Domain/              # Camada de Domínio (Lógica de Negócio)
│   ├── Ecclesiastical/  # Módulo Eclesiástico
│   ├── Financial/       # Módulo Financeiro
│   ├── Secretary/       # Módulo Secretaria
│   ├── Accounts/        # Módulo de Contas/Usuários
│   └── [Módulo]/
│       ├── Actions/           # Lógica de negócio
│       ├── DataTransferObjects/ # DTOs
│       ├── Models/            # Modelos Eloquent
│       ├── Interfaces/        # Contratos/Interfaces
│       └── Constants/         # Constantes do módulo
│
├── Infrastructure/      # Camada de Infraestrutura
│   ├── Repositories/    # Implementação dos Repositories
│   ├── Services/        # Serviços de infraestrutura
│   ├── Exceptions/      # Exceções customizadas
│   └── Util/            # Utilitários de infraestrutura
│
├── Http/                # HTTP específico (Laravel)
└── Providers/           # Service Providers
```

---

## Padrões de Implementação

### 1. Repository Pattern

Os repositories são responsáveis **exclusivamente** pelo acesso aos dados. Toda interação com o banco de dados deve ser feita através de um repository.

#### 📍 Localização
```
app/Infrastructure/Repositories/[Módulo]/[Entidade]/[Entidade]Repository.php
```

#### ✅ Características Obrigatórias

1. **Herdar de `BaseRepository`**
2. **Implementar interface do domínio**
3. **Usar DB Facade do Laravel** (via `$this->model`)
4. **Definir constantes para nomes de colunas**
5. **Definir colunas de seleção (DISPLAY_SELECT_COLUMNS)**
6. **Mapear resultados para DTOs**

#### 📝 Exemplo Completo

```php
<?php

namespace Infrastructure\Repositories\Ecclesiastical\Divisions;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Infrastructure\Repositories\BaseRepository;

class DivisionRepository extends BaseRepository implements DivisionRepositoryInterface
{
    // 1. Model associado
    protected mixed $model = Division::class;

    // 2. Constantes para nomes de tabelas e colunas
    const TABLE_NAME = 'ecclesiastical_divisions';
    const SLUG_COLUMN = 'slug';
    const ENABLED_COLUMN = 'enabled';
    const ID_COLUMN = 'ecclesiastical_divisions.id';
    const NAME_COLUMN = 'ecclesiastical_divisions.name';

    // 3. Colunas de seleção padrão (sempre com alias prefixado)
    const DISPLAY_SELECT_COLUMNS = [
        'ecclesiastical_divisions.id as division_id',
        'ecclesiastical_divisions.route_resource as division_slug',
        'ecclesiastical_divisions.name as division_name',
        'ecclesiastical_divisions.description as division_description',
        'ecclesiastical_divisions.enabled as division_enabled',
        'ecclesiastical_divisions.require_leader as require_leader',
    ];

    /**
     * Buscar divisão por nome
     * SEMPRE mapear o resultado para DTO usando fromResponse()
     */
    public function getDivisionByName(string $division): ?DivisionData
    {
        $result = $this->model
            ->select(self::DISPLAY_SELECT_COLUMNS)
            ->where(
                self::ROUTE_RESOURCE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                $division
            )
            ->first();

        if ($result === null) {
            return null;
        }

        $attributes = $result->getAttributes();
        return DivisionData::fromResponse($attributes);
    }

    /**
     * Buscar divisões com mapeamento de collection
     */
    public function getDivisionsData(int $enabled = 1): Collection
    {
        $divisions = $this->model
            ->select(self::DISPLAY_SELECT_COLUMNS)
            ->where(self::ENABLED_COLUMN, BaseRepository::OPERATORS['EQUALS'], $enabled)
            ->orderBy(self::ID_COLUMN, BaseRepository::ORDERS['ASC'])
            ->get();

        // Mapear cada item para DTO
        return $divisions->map(function ($division) {
            return DivisionData::fromResponse($division->getAttributes());
        });
    }

    /**
     * Criar nova divisão
     */
    public function createDivision(DivisionData $divisionData): Division
    {
        return $this->create([
            'slug'           => $divisionData->slug,
            'name'           => $divisionData->name,
            'description'    => $divisionData->description,
            'enabled'        => $divisionData->enabled,
            'require_leader' => $divisionData->requireLeader,
        ]);
    }
}
```

#### 🔗 JOINs nos Repositories

**SEMPRE fazer joins diretamente na consulta usando DB Facade**

```php
use Illuminate\Support\Facades\DB;

public function getReports(bool $paginate = true): Collection | Paginator
{
    // Combinar colunas de múltiplas tabelas
    $displayColumnsFromRelationship = array_merge(
        self::DISPLAY_SELECT_COLUMNS,
        UserDetailRepository::DISPLAY_SELECT_COLUMNS,
        GroupsRepository::DISPLAY_SELECT_COLUMNS,
        AccountRepository::DISPLAY_SELECT_COLUMNS,
    );

    $query = function () use ($paginate, $displayColumnsFromRelationship) {
        $q = DB::table(self::TABLE_NAME)
            ->select($displayColumnsFromRelationship)
            ->leftJoin(
                UserDetailRepository::TABLE_NAME,
                self::START_BY_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                UserDetailRepository::USER_ID_COLUMN
            )
            ->leftJoin(
                GroupsRepository::TABLE_NAME,
                self::GROUP_RECEIVED_ID_JOINED,
                BaseRepository::OPERATORS['EQUALS'],
                GroupsRepository::ID_COLUMN_JOINED
            )
            ->leftJoin(
                AccountRepository::TABLE_NAME,
                self::ACCOUNT_ID_JOINED,
                BaseRepository::OPERATORS['EQUALS'],
                AccountRepository::ID_COLUMN_JOINED
            )
            ->orderByDesc(self::ID_JOINED);

        if (!$paginate) {
            $result = $q->get();
            // SEMPRE mapear para DTO
            return collect($result)->map(fn($item) => MonthlyReportData::fromResponse((array) $item));
        } else {
            $result = $q->simplePaginate(self::PAGINATE_NUMBER);

            // Mapear paginação para DTO
            $result->setCollection(
                $result->getCollection()->map(fn($item) => MonthlyReportData::fromResponse((array) $item))
            );

            return $result;
        }
    };

    return $this->doQuery($query);
}
```

#### 🎯 Operadores e Ordenação

Use as constantes do `BaseRepository`:

```php
// Operadores
BaseRepository::OPERATORS['EQUALS']      // =
BaseRepository::OPERATORS['NOT_EQUALS']  // <>
BaseRepository::OPERATORS['DIFFERENT']   // !=
BaseRepository::OPERATORS['LIKE']        // LIKE
BaseRepository::OPERATORS['IN']          // IN
BaseRepository::OPERATORS['NOT_IN']      // NOT IN
BaseRepository::OPERATORS['IS_NULL']     // IS NULL
BaseRepository::OPERATORS['NOT_NULL']    // NOT NULL
BaseRepository::OPERATORS['BETWEEN']     // BETWEEN
BaseRepository::OPERATORS['MINOR']       // <
BaseRepository::OPERATORS['MAJOR']       // >

// Ordenação
BaseRepository::ORDERS['ASC']   // ASC
BaseRepository::ORDERS['DESC']  // DESC
```

---

### 2. Data Transfer Objects (DTOs)

DTOs são usados para transferir dados entre camadas, garantindo type-safety e validação.

#### 📍 Localização
```
app/Domain/[Módulo]/DataTransferObjects/[Entidade]Data.php
```

#### ✅ Características Obrigatórias

1. **Extends `Spatie\DataTransferObject\DataTransferObject`**
2. **Propriedades tipadas**
3. **Constantes para nomes de propriedades**
4. **Método estático `fromResponse(array $data): self`** para mapear dados do banco
5. **Pode ter métodos auxiliares de mapeamento privados**

#### 📝 Exemplo Completo

```php
<?php

namespace Domain\Ecclesiastical\Divisions\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DivisionData extends DataTransferObject
{
    // Constantes para nomes de propriedades
    const ID_PROPERTY = 'id';
    const NAME_PROPERTY = 'name';

    // Propriedades tipadas
    public ?int $id;
    public ?string $slug;
    public ?string $name;
    public ?string $description;
    public ?bool $requireLeader;
    public ?bool $enabled;

    /**
     * Mapear dados do banco para DTO
     * SEMPRE usar este método nos repositories
     *
     * @param array $data Dados do banco (colunas com alias)
     * @return self Nova instância do DTO
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'id'            => $data['division_id'] ?? null,
            'slug'          => $data['division_slug'] ?? null,
            'name'          => $data['division_name'] ?? null,
            'description'   => $data['division_description'] ?? null,
            'enabled'       => isset($data['division_enabled']) ? (bool)$data['division_enabled'] : null,
            'requireLeader' => isset($data['require_leader']) ? (bool)$data['require_leader'] : null,
        ]);
    }
}
```

#### 📝 Exemplo com Métodos Auxiliares (Dados Prefixados e Não-Prefixados)

```php
class MemberData extends DataTransferObject
{
    public int $id = 0;
    public ?bool $activated;
    public ?string $fullName;
    public ?string $email;
    // ... outras propriedades

    /**
     * Mapear dados com prefixo (de JOINs)
     */
    private static function getMemberPrefixedData(array $data): array
    {
        return [
            'activated' => isset($data['members_activated']) ? (bool) $data['members_activated'] : null,
            'fullName'  => $data['members_full_name'] ?? null,
            'email'     => $data['members_email'] ?? null,
            'groupIds'  => isset($data['members_group_ids'])
                ? (is_string($data['members_group_ids'])
                    ? json_decode($data['members_group_ids'], true)
                    : $data['members_group_ids'])
                : null,
        ];
    }

    /**
     * Mapear dados sem prefixo (consulta direta)
     */
    private static function getNonPrefixedData(array $data): array
    {
        return [
            'activated' => isset($data['activated']) ? (bool) $data['activated'] : null,
            'fullName'  => $data['full_name'] ?? null,
            'email'     => $data['email'] ?? null,
            'groupIds'  => isset($data['group_ids'])
                ? (is_string($data['group_ids'])
                    ? json_decode($data['group_ids'], true)
                    : $data['group_ids'])
                : null,
        ];
    }

    /**
     * Método público que decide qual mapeamento usar
     */
    public static function fromResponse(array $data): self
    {
        $prefixedData = self::getMemberPrefixedData($data);
        $nonPrefixedData = self::getNonPrefixedData($data);

        // Tenta usar dados prefixados, senão usa não-prefixados
        $mergedData = array_merge(
            ['id' => $data['members_id'] ?? $data['id'] ?? 0],
            array_filter($prefixedData, fn ($value) => $value !== null) ?:
                array_filter($nonPrefixedData, fn ($value) => $value !== null)
        );

        return new self($mergedData);
    }
}
```

---

### 3. Actions Pattern

**TODA a lógica de negócio deve estar nas Actions**. Actions orquestram a execução de regras de negócio, validações complexas e coordenação entre múltiplos repositories.

#### 📍 Localização
```
app/Domain/[Módulo]/Actions/[Verbo][Entidade]Action.php
```

#### ✅ Características Obrigatórias

1. **Injeção de dependências via construtor**
2. **Método público `execute()` com assinatura clara**
3. **Receber DTOs como parâmetros**
4. **Retornar Models, DTOs ou Collections**
5. **Lançar exceções de negócio quando necessário**
6. **Não conter consultas SQL (delegar aos repositories)**

#### 📝 Exemplo Completo

```php
<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Domain\Ecclesiastical\Divisions\Constants\ReturnMessages;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Throwable;

class CreateNewDivisionAction
{
    private DivisionRepository $divisionRepository;

    /**
     * Injeção de dependências
     */
    public function __construct(
        DivisionRepositoryInterface $divisionRepository,
    ) {
        $this->divisionRepository = $divisionRepository;
    }

    /**
     * Executar lógica de negócio
     *
     * @param DivisionData $divisionData Dados validados
     * @param string $tenant Tenant atual
     * @return Division Divisão criada
     * @throws Throwable
     */
    public function execute(DivisionData $divisionData, string $tenant): Division
    {
        // 1. Validação de regra de negócio
        $existDivision = $this->divisionRepository->getDivisionByName($divisionData->slug);

        if (is_null($existDivision)) {
            // 2. Criar entidade
            $division = $this->divisionRepository->createDivision($divisionData);

            // 3. Validar criação
            if (!is_null($division->id)) {
                return $division;
            } else {
                throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_DIVISION, 500);
            }
        } else {
            // 4. Lançar exceção de negócio
            throw new GeneralExceptions(ReturnMessages::ERROR_ALREADY_DIVISION, 500);
        }
    }
}
```

#### 🎯 Nomenclatura de Actions

```
Create[Entidade]Action      # Criar
Update[Entidade]Action      # Atualizar
Delete[Entidade]Action      # Deletar
Get[Entidade]Action         # Buscar único
Get[Entidades]Action        # Buscar múltiplos
Approve[Entidade]Action     # Aprovar
Process[Entidade]Action     # Processar
Calculate[Algo]Action       # Calcular
Validate[Algo]Action        # Validar
Send[Algo]Action            # Enviar
```

---

### 4. Services Pattern

Services são usados para **funcionalidades técnicas e integrações externas**, não para lógica de negócio.

#### 📍 Localização
```
app/Infrastructure/Services/[Categoria]/[Nome]Service.php
```

#### ✅ Quando Usar Services

- ✅ Integração com APIs externas (Google Drive, S3, etc)
- ✅ Processamento técnico (OCR, LLM, conversão de arquivos)
- ✅ Utilitários de infraestrutura (storage, email, etc)

#### ❌ Quando NÃO Usar Services

- ❌ Lógica de negócio (use Actions)
- ❌ Acesso a dados (use Repositories)
- ❌ Validação de regras de negócio (use Actions)

#### 📝 Exemplo

```php
<?php

namespace Infrastructure\Services\External\minIO;

use Aws\S3\Exception\S3Exception;
use Illuminate\Http\UploadedFile;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\ConnectS3;

class MinioStorageService
{
    private ConnectS3 $s3;

    public function __construct(ConnectS3 $connectS3)
    {
        $this->s3 = $connectS3;
    }

    /**
     * Upload de arquivo para MinIO
     */
    public function upload($file, string $relativePath, string $tenant, bool $processError = false): string
    {
        if (is_string($file)) {
            $file = new UploadedFile($file, basename($file), null, null, true);
        }

        $env = App::environment();
        $timestamp = time();
        $formattedTime = date("YmdHis", $timestamp);
        $baseUrl = config('services-hosts.services.s3.environments.' . $env . '.S3_ENDPOINT_EXTERNAL_ACCESS');
        $fileExtension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $fileName = $processError
            ? 'ERROR_' . $formattedTime . '_' . uniqid() . '.' . $fileExtension
            : $formattedTime . '_' . uniqid() . '.' . $fileExtension;
        $fullPathFile = $relativePath . '/' . $fileName;
        $contentType = $file->getMimeType();

        try {
            $s3 = $this->s3->getInstance();
            $s3->putObject([
                'Bucket'      => $tenant,
                'Key'         => $fullPathFile,
                'Body'        => file_get_contents($file),
                'ACL'         => 'public-read',
                'ContentType' => $contentType
            ]);

            return $baseUrl . '/' . $tenant . '/' . $fullPathFile;

        } catch (S3Exception $e) {
            throw new GeneralExceptions(ConnectS3::UPLOAD_FILE_ERROR_S3, 500, $e);
        }
    }

    /**
     * Deletar arquivo do MinIO
     */
    public function delete(string $filePath, string $tenant): bool
    {
        try {
            $s3 = $this->s3->getInstance();
            $s3->deleteObject([
                'Bucket' => $tenant,
                'Key'    => $filePath
            ]);
            return true;
        } catch (S3Exception $e) {
            throw new GeneralExceptions("Error deleting file from MinIO", 500);
        }
    }
}
```

---

### 5. Controllers

Controllers são responsáveis **apenas por orquestrar requisições HTTP**. Não devem conter lógica de negócio.

#### 📍 Localização
```
app/Application/Api/v1/[Módulo]/Controllers/[Entidade]Controller.php
```

#### ✅ Responsabilidades do Controller

1. ✅ Receber requisições HTTP
2. ✅ Validar dados (via FormRequest)
3. ✅ Chamar Actions
4. ✅ Retornar respostas HTTP
5. ✅ Tratar exceções

#### ❌ O que NÃO fazer no Controller

- ❌ Lógica de negócio
- ❌ Consultas ao banco
- ❌ Validações complexas de regras de negócio

#### 📝 Exemplo Completo

```php
<?php

namespace Application\Api\v1\Ecclesiastical\Divisions\Controllers;

use Application\Api\v1\Ecclesiastical\Divisions\Requests\DivisionRequest;
use Application\Api\v1\Ecclesiastical\Divisions\Resources\DivisionsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Divisions\Actions\CreateNewDivisionAction;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionsAction;
use Domain\Ecclesiastical\Divisions\Constants\ReturnMessages;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class DivisionsController extends Controller
{
    /**
     * Criar nova divisão
     */
    public function createDivision(
        DivisionRequest $divisionRequest,
        CreateNewDivisionAction $createNewDivisionAction
    ): Application|Response|ResponseFactory
    {
        try {
            // 1. Extrair informações do request
            $tenant = explode('.', $divisionRequest->getHost())[0];

            // 2. Chamar Action (toda lógica está na Action)
            $createNewDivisionAction->execute(
                $divisionRequest->divisionData(),
                $tenant
            );

            // 3. Retornar resposta HTTP
            return response([
                'message' => ReturnMessages::DIVISION_CREATED,
            ], 201);

        } catch(GeneralExceptions $e) {
            // 4. Tratar exceções
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Listar divisões
     */
    public function getDivisions(
        Request $request,
        GetDivisionsAction $getDivisionsAction
    ): DivisionsResourceCollection
    {
        try {
            // 1. Extrair parâmetros
            $enabled = $request->has('enabled')
                ? (int) $request->input('enabled')
                : null;

            // 2. Chamar Action
            $response = $getDivisionsAction->execute($enabled);

            // 3. Retornar Resource Collection
            return new DivisionsResourceCollection($response);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
```

---

### 6. Form Requests

Form Requests são usados para validação de dados de entrada e transformação em DTOs.

#### 📍 Localização
```
app/Application/Api/v1/[Módulo]/Requests/[Entidade]Request.php
```

#### ✅ Responsabilidades

1. ✅ Definir regras de validação
2. ✅ Definir mensagens customizadas
3. ✅ Transformar dados validados em DTOs (método helper)

#### 📝 Exemplo Completo

```php
<?php

namespace Application\Api\v1\Ecclesiastical\Divisions\Requests;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DivisionRequest extends FormRequest
{
    /**
     * Autorização
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação
     */
    public function rules(): array
    {
        return [
            'slug'          => 'required',
            'name'          => 'required',
            'description'   => '',
            'requireLeader' => '',
            'enabled'       => 'required',
        ];
    }

    /**
     * Mensagens customizadas
     */
    public function messages(): array
    {
        return [
            'name.required'    => "O preenchimento do nome da divisão é obrigatório!",
            'slug.required'    => "O Slug deve ser enviado juntamente com os dados preenchidos!",
            'enabled.required' => "O campo enabled deve ser enviado!",
        ];
    }

    /**
     * Transformar dados validados em DTO
     *
     * @return DivisionData
     * @throws UnknownProperties
     */
    public function divisionData(): DivisionData
    {
        return new DivisionData(
            slug:          $this->input('slug'),
            name:          $this->input('name'),
            description:   $this->input('description'),
            requireLeader: $this->input('requireLeader'),
            enabled:       $this->input('enabled'),
        );
    }
}
```

---

## Fluxo de uma Requisição

```
┌─────────────────────────────────────────────────────────────────┐
│                         FLUXO COMPLETO                          │
└─────────────────────────────────────────────────────────────────┘

1. REQUEST HTTP
   │
   ├─> Routes (routes/api.php)
   │
   └─> 2. CONTROLLER (Application/Api/v1/.../Controllers/)
           │
           ├─> FormRequest valida dados
           │   └─> Converte para DTO
           │
           └─> 3. ACTION (Domain/.../Actions/)
                   │
                   ├─> Executa lógica de negócio
                   ├─> Valida regras de negócio
                   │
                   └─> 4. REPOSITORY (Infrastructure/Repositories/)
                           │
                           ├─> Consulta banco de dados (DB Facade)
                           ├─> Executa JOINs se necessário
                           │
                           └─> Mapeia resultados para DTO (fromResponse)

                   ← Retorna DTO/Model/Collection

           ← Retorna Response HTTP (JSON)

   ← 5. RESPONSE HTTP
```

### Exemplo Detalhado

```php
// 1. ROTA
Route::post('/divisions', [DivisionsController::class, 'createDivision']);

// 2. CONTROLLER
public function createDivision(DivisionRequest $request, CreateNewDivisionAction $action)
{
    $result = $action->execute($request->divisionData(), $tenant);
    return response(['message' => 'Criado com sucesso'], 201);
}

// 3. ACTION
public function execute(DivisionData $divisionData, string $tenant): Division
{
    // Validação de negócio
    $exists = $this->repository->getDivisionByName($divisionData->slug);

    if ($exists) {
        throw new GeneralExceptions('Já existe', 500);
    }

    // Criar
    return $this->repository->createDivision($divisionData);
}

// 4. REPOSITORY
public function getDivisionByName(string $division): ?DivisionData
{
    $result = $this->model
        ->select(self::DISPLAY_SELECT_COLUMNS)
        ->where(self::ROUTE_RESOURCE_COLUMN, '=', $division)
        ->first();

    return $result ? DivisionData::fromResponse($result->getAttributes()) : null;
}

// 5. DTO
public static function fromResponse(array $data): self
{
    return new self([
        'id'   => $data['division_id'] ?? null,
        'name' => $data['division_name'] ?? null,
        // ...
    ]);
}
```

---

## Convenções e Boas Práticas

### 🎯 Repositories

```php
✅ SEMPRE use constantes para nomes de colunas
✅ SEMPRE use DISPLAY_SELECT_COLUMNS com alias prefixados
✅ SEMPRE mapeie resultados para DTOs usando fromResponse()
✅ SEMPRE use DB Facade ($this->model ou DB::table())
✅ SEMPRE faça JOINs diretamente nas queries
✅ Use BaseRepository::OPERATORS para operadores
✅ Use BaseRepository::ORDERS para ordenação

❌ NUNCA retorne arrays simples
❌ NUNCA coloque lógica de negócio no repository
❌ NUNCA use relacionamentos Eloquent para JOINs (prefira JOINs manuais)
```

### 🎯 DTOs

```php
✅ SEMPRE extends DataTransferObject
✅ SEMPRE defina propriedades tipadas
✅ SEMPRE implemente fromResponse() estático
✅ Use constantes para nomes de propriedades
✅ Converta tipos corretamente (bool, int, json_decode)
✅ Use métodos auxiliares privados para mapeamentos complexos

❌ NUNCA coloque lógica de negócio em DTOs
❌ NUNCA faça consultas ao banco em DTOs
```

### 🎯 Actions

```php
✅ SEMPRE coloque TODA lógica de negócio nas Actions
✅ SEMPRE injete dependências via construtor
✅ SEMPRE use método execute() como ponto de entrada
✅ SEMPRE receba DTOs como parâmetros
✅ SEMPRE retorne Models, DTOs ou Collections
✅ Lance GeneralExceptions para erros de negócio
✅ Use constantes para mensagens de retorno

❌ NUNCA faça consultas SQL diretamente (use repositories)
❌ NUNCA retorne Response HTTP (isso é do controller)
❌ NUNCA acesse Request diretamente
```

### 🎯 Controllers

```php
✅ SEMPRE use FormRequests para validação
✅ SEMPRE injete Actions via parâmetros do método
✅ SEMPRE retorne Response HTTP (response(), json())
✅ Use try/catch para tratar exceções
✅ Extraia dados do Request e passe para Action

❌ NUNCA coloque lógica de negócio no controller
❌ NUNCA faça consultas ao banco diretamente
❌ NUNCA use Repositories diretamente (use Actions)
```

### 🎯 Nomenclatura

```php
// Arquivos e Classes
[Entidade]Repository.php        # GroupRepository, UserRepository
[Entidade]Data.php              # GroupData, UserData
[Verbo][Entidade]Action.php     # CreateGroupAction, GetUserAction
[Entidade]Controller.php        # GroupsController, UsersController
[Entidade]Request.php           # GroupRequest, UserRequest

// Constantes de Colunas
const TABLE_NAME = 'nome_tabela';
const ID_COLUMN = 'tabela.id';
const NAME_COLUMN = 'tabela.name';
const ID_COLUMN_JOINED = 'tabela.id';  // Para JOINs

// Select Columns (sempre com alias prefixado)
const DISPLAY_SELECT_COLUMNS = [
    'tabela.id as entidade_id',
    'tabela.name as entidade_name',
];

// Métodos
public function get[Entidade]ByName()      # getDivisionByName()
public function get[Entidades]()           # getDivisions()
public function create[Entidade]()         # createDivision()
public function update[Entidade]()         # updateDivision()
public function delete[Entidade]()         # deleteDivision()
```

### 🎯 Mapeamento de Dados

```php
// Em Repositories: SEMPRE mapear para DTO
$result = $this->model->select(self::DISPLAY_SELECT_COLUMNS)->first();
return DivisionData::fromResponse($result->getAttributes());

// Para Collections
return $results->map(fn($item) => DivisionData::fromResponse($item->getAttributes()));

// Para Paginação
$paginated = $query->paginate(15);
$paginated->setCollection(
    $paginated->getCollection()->map(fn($item) => DivisionData::fromResponse((array) $item))
);

// Em DTOs: Usar operador null coalescing
'name' => $data['division_name'] ?? null,
'enabled' => isset($data['division_enabled']) ? (bool)$data['division_enabled'] : null,

// Para JSON: Decodificar strings
'groupIds' => isset($data['group_ids'])
    ? (is_string($data['group_ids']) ? json_decode($data['group_ids'], true) : $data['group_ids'])
    : null,
```

### 🎯 Tratamento de Exceções

```php
// Use GeneralExceptions para erros de negócio
use Infrastructure\Exceptions\GeneralExceptions;

throw new GeneralExceptions('Mensagem de erro', 500);
throw new GeneralExceptions(ReturnMessages::ERROR_ALREADY_EXISTS, 400);

// Em Controllers: Sempre capturar e re-lançar
try {
    $action->execute($data);
} catch (GeneralExceptions $e) {
    throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
}
```

---

## ✅ Checklist de Implementação

### Ao criar um novo módulo:

- [ ] Criar estrutura de pastas no Domain/
  - [ ] Actions/
  - [ ] DataTransferObjects/
  - [ ] Models/
  - [ ] Interfaces/
  - [ ] Constants/

- [ ] Criar estrutura no Infrastructure/
  - [ ] Repositories/[Módulo]/

- [ ] Criar estrutura no Application/
  - [ ] Api/v1/[Módulo]/Controllers/
  - [ ] Api/v1/[Módulo]/Requests/
  - [ ] Api/v1/[Módulo]/Resources/

### Ao criar um Repository:

- [ ] Extends BaseRepository
- [ ] Implements Interface do Domain
- [ ] Definir constantes (TABLE_NAME, colunas, etc)
- [ ] Definir DISPLAY_SELECT_COLUMNS com alias prefixados
- [ ] SEMPRE mapear resultados para DTOs
- [ ] Usar DB Facade para queries
- [ ] Fazer JOINs diretamente nas queries

### Ao criar um DTO:

- [ ] Extends DataTransferObject
- [ ] Propriedades tipadas e públicas
- [ ] Constantes para nomes de propriedades
- [ ] Implementar fromResponse() estático
- [ ] Métodos auxiliares privados se necessário

### Ao criar uma Action:

- [ ] Injeção de dependências no construtor
- [ ] Método execute() público
- [ ] Receber DTOs como parâmetros
- [ ] Toda lógica de negócio na Action
- [ ] Retornar Models, DTOs ou Collections
- [ ] Lançar exceções de negócio quando necessário

### Ao criar um Controller:

- [ ] Usar FormRequest para validação
- [ ] Injetar Actions nos métodos
- [ ] APENAS orquestrar requisições
- [ ] Retornar Response HTTP
- [ ] Try/catch para exceções

---

## 📚 Referências Rápidas

### BaseRepository - Métodos Disponíveis

```php
// Buscar
$repository->getAll($columns, $orderBy, $sort)
$repository->getPaginated($paged, $orderBy, $sort)
$repository->getById($id)
$repository->getItemByColumn($column, $operator, $term)
$repository->getItemsByColumn($column, $term, $orderBy, $sort)
$repository->getItemByWhere($columns, $conditions)
$repository->getItemsWithRelationshipsAndWheres($conditions, $orderBy, $sort)

// Criar/Atualizar/Deletar
$repository->create($data)
$repository->update($conditions, $data)
$repository->updateOrCreate($data, $identifiers)
$repository->delete($id)
$repository->deleteByColumn($column, $data)

// Helpers
$repository->exists($id)
$repository->with($relationships)  // Para eager loading
```

### BaseRepository - Helpers de Condições

```php
$repository->whereEqual($column, $value, $whereType)
$repository->whereLike($column, $value, $whereType)
$repository->whereIn($column, $value, $whereType)
$repository->whereNotIn($column, $value, $whereType)
$repository->whereIsNull($column, $whereType)
$repository->whereBetween($column, $value, $whereType)
```

---

## 🔍 Exemplos de Uso Comum

### Buscar e Retornar DTO

```php
// Repository
public function getUserById(int $id): ?UserData
{
    $result = $this->model
        ->select(self::DISPLAY_SELECT_COLUMNS)
        ->where('id', '=', $id)
        ->first();

    return $result ? UserData::fromResponse($result->getAttributes()) : null;
}
```

### Buscar com JOIN e Retornar Collection de DTOs

```php
// Repository
public function getMembersWithGroups(): Collection
{
    $columns = array_merge(
        self::DISPLAY_SELECT_COLUMNS,
        GroupRepository::DISPLAY_SELECT_COLUMNS
    );

    $results = DB::table(self::TABLE_NAME)
        ->select($columns)
        ->leftJoin(
            GroupRepository::TABLE_NAME,
            self::GROUP_ID_COLUMN,
            '=',
            GroupRepository::ID_COLUMN
        )
        ->get();

    return collect($results)->map(fn($item) => MemberData::fromResponse((array) $item));
}
```

### Action com Múltiplos Repositories

```php
// Action
public function execute(CreateOrderData $orderData): Order
{
    // 1. Validar produto existe
    $product = $this->productRepository->getById($orderData->productId);
    if (!$product) {
        throw new GeneralExceptions('Produto não encontrado', 404);
    }

    // 2. Validar estoque
    if ($product->stock < $orderData->quantity) {
        throw new GeneralExceptions('Estoque insuficiente', 400);
    }

    // 3. Criar pedido
    $order = $this->orderRepository->createOrder($orderData);

    // 4. Atualizar estoque
    $this->productRepository->updateStock($product->id, $product->stock - $orderData->quantity);

    return $order;
}
```

---

## 📖 Conclusão

Este documento descreve os padrões arquiteturais utilizados no projeto ATOS8. Seguir estes padrões garante:

- ✅ **Código limpo e organizado**
- ✅ **Separação clara de responsabilidades**
- ✅ **Facilidade de manutenção**
- ✅ **Testabilidade**
- ✅ **Consistência entre módulos**
- ✅ **Type-safety com DTOs**

**Sempre consulte este documento ao desenvolver novas funcionalidades!**

---

**Última atualização**: 2026-01-01
**Versão**: 1.0.0
**Projeto**: ATOS8 - Church Management Platform
