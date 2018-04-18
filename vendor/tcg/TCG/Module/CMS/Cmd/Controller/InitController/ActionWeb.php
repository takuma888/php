<?php

namespace TCG\Module\CMS\Cmd\Controller\InitController;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TCG\Module\CMS\Cmd\Controller\CmdAction;

class ActionWeb extends CmdAction
{

    protected function configure()
    {
        $this
            ->setName('tcg_cms:init.web')
            ->setDescription('创建CMS使用到的CSS和Javascript');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publicRoot = getContainer()->getParameter('tcg_module.cms.web_asset.public_root');
        if (!realpath($publicRoot)) {
            mkdir($publicRoot, 0755, true);
        }
        $sourceRoot = getContainer()->getParameter('tcg_module.cms.web_asset.src_root');

        $relativePath = $this->tcgCMF()
            ->toolDirectory()
            ->relativePath($sourceRoot, $publicRoot);

        $this->tcgCMF()
            ->toolDirectory()
            ->makeSymLink($relativePath, $publicRoot . '/cms');
    }
}