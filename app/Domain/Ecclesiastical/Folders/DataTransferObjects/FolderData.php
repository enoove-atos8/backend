<?php

namespace Domain\Ecclesiastical\Folders\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class FolderData extends DataTransferObject
{
    /** @var string | null  */
    public string | null $ecclesiasticalDivisionsGroupId;

    /** @var string | null  */
    public string | null $folderId;

    /** @var string | null  */
    public string | null $folderName;

    /** @var boolean | null  */
    public bool | null $folderDevolution;

    /** @var string | null  */
    public string | null $entryType;
}
