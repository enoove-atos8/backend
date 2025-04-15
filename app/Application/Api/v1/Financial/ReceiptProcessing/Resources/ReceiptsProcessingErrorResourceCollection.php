<?php

namespace Application\Api\v1\Financial\ReceiptProcessing\Resources;

use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\ReceiptProcessing\DataTransferObjects\ReceiptProcessingData;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ReceiptsProcessingErrorResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'receipts';


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [
            'notProcessed'     =>  [],
            'notMapped'        =>  [],
        ];
        $receipts = $this->collection;

        foreach ($receipts as $receipt)
        {
            if($receipt->reason == 'READING_ERROR')
                $result['notProcessed'][] = $this->mountResource($receipt);

            else if($receipt->reason == 'NOT_MAPPED')
                $result['notMapped'][] = $this->mountResource($receipt);

        }

        return $result;
    }



    /**
     * @param ReceiptProcessingData $receipt
     * @return array
     */
    public function mountResource(ReceiptProcessingData $receipt): array
    {
        return [
            'id'                =>  $receipt->id,
            'docType'           =>  $receipt->docType,
            'docSubType'        =>  $receipt->docSubType,
            'amount'            =>  $receipt->amount,
            'reason'            =>  $receipt->reason,
            'status'            =>  $receipt->status,
            'institution'       =>  $receipt->institution,
            'devolution'        =>  $receipt->devolution,
            'isPayment'         =>  $receipt->isPayment,
            'deleted'           =>  $receipt->deleted,
            'receiptLink'       =>  $receipt->receiptLink,
            'division'          =>  $this->getDivision($receipt->division),
            'groupReturned'     =>  $this->getGroup($receipt->groupReturned),
            'groupReceived'     =>  $this->getGroup($receipt->groupReceived),
            'paymentCategory'   =>  $this->getPaymentCategory($receipt->paymentCategory),
            'paymentItem'       =>  $this->getPaymentItem($receipt->paymentItem),
        ];
    }




    /**
     * @param DivisionData $divisionData
     * @return array|null
     */
    public function getDivision(DivisionData $divisionData): ?array
    {
        if(!is_null($divisionData->id))
        {
            return [
                'id'            =>  $divisionData->id,
                'slug'          =>  $divisionData->slug,
                'name'          =>  $divisionData->name,
                'description'   =>  $divisionData->description,
                'enabled'       =>  $divisionData->enabled,
            ];
        }
        else
        {
            return null;
        }
    }


    /**
     * @param GroupData $groupData
     * @return array|null
     */
    public function getGroup(GroupData $groupData): ?array
    {
        if(!is_null($groupData->id))
        {
            return [
                'id'                    =>  $groupData->id,
                'divisionId'            =>  $groupData->divisionId,
                'parentGroupId'         =>  $groupData->parentGroupId,
                'leaderId'              =>  $groupData->leaderId,
                'name'                  =>  $groupData->name,
                'description'           =>  $groupData->description,
                'slug'                  =>  $groupData->slug,
                'enabled'               =>  $groupData->enabled,
                'temporaryEvent'        =>  $groupData->temporaryEvent,
                'returnValues'          =>  $groupData->returnValues,
                'financialGroup'        =>  $groupData->financialGroup,
                'startDate'             =>  $groupData->startDate,
                'endDate'               =>  $groupData->endDate,
            ];
        }
        else
        {
            return null;
        }
    }


    /**
     * @param PaymentCategoryData $paymentCategoryData
     * @return array|null
     */
    public function getPaymentCategory(PaymentCategoryData $paymentCategoryData): ?array
    {
        if(!is_null($paymentCategoryData->id))
        {
            return [
                'id'            =>  $paymentCategoryData->id,
                'slug'          =>  $paymentCategoryData->slug,
                'name'          =>  $paymentCategoryData->name,
                'description'   =>  $paymentCategoryData->description,
            ];
        }
        else
        {
            return null;
        }
    }



    /**
     * @param PaymentItemData $paymentItemData
     * @return array|null
     */
    public function getPaymentItem(PaymentItemData $paymentItemData): ?array
    {
        if(!is_null($paymentItemData->id))
        {
            return [
                'id'                    =>  $paymentItemData->id,
                'paymentCategoryId'     =>  $paymentItemData->paymentCategoryId,
                'slug'                  =>  $paymentItemData->slug,
                'name'                  =>  $paymentItemData->name,
                'description'           =>  $paymentItemData->description,
            ];
        }
        else
        {
            return null;
        }
    }
}
