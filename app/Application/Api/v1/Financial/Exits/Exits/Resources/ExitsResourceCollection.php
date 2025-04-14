<?php

namespace Application\Api\v1\Financial\Exits\Exits\Resources;

use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use App\Domain\Financial\Reviewers\DataTransferObjects\FinancialReviewerData;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ExitsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'exits';


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];
        $exits = $this->collection;

        foreach ($exits as $exit)
        {
            $result[] = [
                'id'                             =>  $exit->id,
                'exitType'                       =>  $exit->exitType,
                'isPayment'                      =>  $exit->isPayment,
                'deleted'                        =>  $exit->deleted,
                'transactionType'                =>  $exit->transactionType,
                'transactionCompensation'        =>  $exit->transactionCompensation,
                'dateTransactionCompensation'    =>  $exit->dateTransactionCompensation,
                'dateExitRegister'               =>  $exit->dateExitRegister,
                'timestampExitTransaction'       =>  $exit->timestampExitTransaction,
                'amount'                         =>  $exit->amount,
                'comments'                       =>  $exit->comments,
                'receiptLink'                    =>  $exit->receiptLink,
                'reviewer'                       =>  $this->getReviewer($exit->financialReviewer),
                'division'                       =>  $this->getDivision($exit->division),
                'group'                          =>  $this->getGroup($exit->group),
                'paymentCategory'                =>  $this->getPaymentCategory($exit->paymentCategory),
                'paymentItem'                    =>  $this->getPaymentItem($exit->paymentItem),
            ];
        }

        return $result;
    }




    /**
     * @param FinancialReviewerData $data
     * @return array|null
     */
    public function getReviewer(FinancialReviewerData $data): ?array
    {
        if(!is_null($data->id))
        {
            return [
                'id'             =>  $data->id,
                'fullName'       =>  $data->fullName,
                'reviewerType'   =>  $data->reviewerType,
                'avatar'         =>  $data->avatar,
                'gender'         =>  $data->gender,
                'cpf'            =>  $data->cpf,
                'rg'             =>  $data->rg,
                'email'          =>  $data->email,
                'cellPhone'      =>  $data->cellPhone,
                'activated'      =>  $data->activated,
                'deleted'        =>  $data->deleted,
            ];
        }
        else
        {
            return null;
        }

    }


    /**
     * @param DivisionData $data
     * @return array|null
     */
    public function getDivision(DivisionData $data): ?array
    {
        if(!is_null($data->id))
        {
            return [
                'id'            =>  $data->id,
                'slug'          =>  $data->slug,
                'name'          =>  $data->name,
                'description'   =>  $data->description,
                'enabled'       =>  $data->enabled,
            ];
        }
        else
        {
            return null;
        }

    }



    /**
     * @param GroupData $data
     * @return array|null
     */
    public function getGroup(GroupData $data): ?array
    {
        if(!is_null($data->id))
        {
            return [
                'id'            =>  $data->id,
                'name'          =>  $data->name,
                'divisionId'    =>  $data->divisionId,
                'description'   =>  $data->description,
                'slug'          =>  $data->slug,
                'enabled'       =>  $data->enabled,
            ];
        }
        else
        {
            return null;
        }

    }


    /**
     * @param PaymentCategoryData $data
     * @return array|null
     */
    public function getPaymentCategory(PaymentCategoryData $data): ?array
    {
        if(!is_null($data->id))
        {
            return [
                'id'            =>  $data->id,
                'slug'          =>  $data->slug,
                'name'          =>  $data->name,
                'description'   =>  $data->description,
            ];
        }
        else
        {
            return null;
        }
    }


    /**
     * @param PaymentItemData $data
     * @return array|null
     */
    public function getPaymentItem(PaymentItemData $data): ?array
    {
        if(!is_null($data->id))
        {
            return [
                'id'                    =>  $data->id,
                'paymentCategoryId'     =>  $data->paymentCategoryId,
                'slug'                  =>  $data->slug,
                'name'                  =>  $data->name,
                'description'           =>  $data->description,
            ];
        }
        else
        {
            return null;
        }
    }



    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
