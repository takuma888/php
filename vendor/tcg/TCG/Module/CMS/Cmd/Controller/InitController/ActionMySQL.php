<?php

namespace TCG\Module\CMS\Cmd\Controller\InitController;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TCG\Module\CMS\Cmd\Controller\CmdAction;

class ActionMySQL extends CmdAction
{
    protected function configure()
    {
        $this
            ->setName('tcg_cms.init.mysql')
            ->setDescription('创建CMS使用到的MySQL库和表')->addOption('show-sql', null, InputOption::VALUE_OPTIONAL, '是否显示SQL', 0)
            ->addOption('drop', null, InputOption::VALUE_OPTIONAL, '是否drop已存在的表', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $show_sql = $input->getOption('show-sql');
        $drop = $input->getOption('drop');

        $output->writeln('<info>初始化 主 mysql</info>');

        $output->writeln('<question>创建 sessions 表</question>');
        $sql = $this->dbMain()->tblSessions()->create([
            'drop' => $drop,
            'exec' => true,
        ]);
        if ($show_sql) {
            $output->writeln("<comment>{$sql}</comment>");
        }
    }
}