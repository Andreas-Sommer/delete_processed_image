<?php

namespace Belsignum\DeleteProcessedImage\Backend\FileList;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\Buttons\GenericButton;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Filelist\Event\ProcessFileListActionsEvent;

final readonly class DeleteProcessedImageFileListActionListener
{
    public function __construct(
        private UriBuilder   $backendUriBuilder,
        private IconFactory  $iconFactory,
        private PageRenderer $pageRenderer
    )
    {
        $this->pageRenderer->loadJavaScriptModule('@belsignum/delete-processed-image/delete-processed-image-handler.js');
    }

    public function __invoke(ProcessFileListActionsEvent $event): void
    {
        $file = $event->getResource();

        if (!$file instanceof File)
        {
            return;
        }

        // Generiere Identifier (string!)
        $identifier = $file->getCombinedIdentifier();

        // URL zur Controller-Route erzeugen
        $url = $this->backendUriBuilder
            ->buildUriFromRoute('delete-processed-image', ['identifier' => $identifier])
            ->__toString();

        // Button erzeugen
        $button = GeneralUtility::makeInstance(GenericButton::class);
        $button
            ->setIcon($this->iconFactory->getIcon('actions-edit-delete', Icon::SIZE_SMALL))
            ->setTag('button')
            ->setLabel('Delete processed files')
            ->setAttributes([
                'type'        => 'button',
                'data-action' => 'delete-processed-image',
                'data-url'    => $url,
                'class'       => 'btn btn-warning',
                'title'       => 'Delete all processed files for this image',
            ])
            ->setShowLabelText(false);

        // Zur Liste hinzufügen
        $actions = $event->getActionItems();
        $actions['delete-processed-image'] = $button;
        $event->setActionItems($actions);
    }
}