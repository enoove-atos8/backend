<?php

namespace Application\Api\v1\Church\Controllers;

use Application\Api\v1\Church\Requests\ChurchRequest;
use Application\Api\v1\Church\Resources\ChurchResource;
use Application\Api\v1\Church\Resources\ErrorChurchResource;
use Application\Api\v1\Church\Resources\ChurchResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Churches\Actions\CreateChurchAction;
use Domain\Churches\Models\Church;
use Domain\Users\Actions\CreateUserAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class ChurchController extends Controller
{
    /**
     * @OA\Post(
     * path="/v1/church",
     * security={{"Bearer": {}}},
     * tags={"Church"},
     * summary="Register a new church to access the platform",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *          @OA\Property(property="tenant_id", type="integer", example="ibrr"),
     *          @OA\Property(property="name", type="string", example="Igreja Batista Reformada do Recife"),
     *          @OA\Property(property="activated", type="boolean", example=false),
     *          @OA\Property(property="doc_type", type="string", example="cnpj/cpf"),
     *          @OA\Property(property="doc_number", type="string", example="53575672000191/90441944027"),
     *          @OA\Property(property="admin_email_tenant", type="string", example="admin_email_tenant@ibrr.com.br"),
     *          @OA\Property(property="pass_admin_email_tenant", type="string", example="123456"),
     *          @OA\Property(property="confirm_pass_admin_email_tenant", type="string", example="same password"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="New church registred successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="tenant_id", type="integer", example="ibrr"),
     *              @OA\Property(property="name", type="string", example="Igreja Batista Reformada do Recife"),
     *              @OA\Property(property="activated", type="boolean", example=false),
     *              @OA\Property(property="doc_type", type="string", example="cnpj/cpf"),
     *              @OA\Property(property="doc_number", type="string", example="53575672000191/90441944027"),
     *              @OA\Property(property="admin_email_tenant", type="string", example="admin_email_tenant@ibrr.com.br"),
     *              @OA\Property(property="pass_admin_email_tenant", type="string", example="123456"),
     *              @OA\Property(property="confirm_pass_admin_email_tenant", type="string", example="123456"),
     *              )
     *       ),
     *      @OA\Response(response=422,description="Unprocessable Entity"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param ChurchRequest $churchRequest
     * @param CreateChurchAction $createChurchAction
     * @return ChurchResource
     * @throws TenantCouldNotBeIdentifiedById
     * @throws UnknownProperties
     * @throws \Throwable
     */
    public function createChurch(ChurchRequest $churchRequest, CreateChurchAction $createChurchAction): ChurchResource
    {
        try {
            $response = $createChurchAction($churchRequest->churchData());
            return new ChurchResource($response); // TODO: implementar retorno para as clases de resources

        }catch(\Exception $e){
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
