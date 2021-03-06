<?php

namespace SilverStripe\Upgrader\Console;

use SilverStripe\Upgrader\CodeCollection\CodeChangeSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Implementation of the AutomatedCommand Interface that should be suitable to most Command.
 *
 * Command using this trait are responsible for:
 * * implement a `enrichArgs` method ;
 * * updating $args and $diff.
 */
trait AutomatedCommandTrait
{

    /**
     * @var CodeChangeSet
     */
    private $diff;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var bool
     */
    private $isBeingRunAutomated = false;


    /**
     * Run this command in automated mode.
     * @param array $args Minimal arguments this command needs to run automated.
     * @param OutputInterface $output
     * @return int
     */
    public function automatedRun(array $args, OutputInterface $output): int
    {
        // Enable the isAutomated flag.
        $this->isBeingRunAutomated = true;

        $this->args = $args;

        return $this->run(
            new ArrayInput($this->enrichArgs($args)),
            $output
        );
    }

    /**
     * Run the command.
     * @internal Command have a run command out of the box.
     * @param InputInterface $in
     * @param OutputInterface $output
     * @return int
     */
    public abstract function run(InputInterface $in, OutputInterface $output);

    /**
     * Return the Code Change Set generated by the command, if any.
     * @return CodeChangeSet
     */
    public function getDiff(): CodeChangeSet
    {
        return $this->diff ?: new CodeChangeSet();
    }

    /**
     * Build the updated argument list following the execution of the command. This should be used by command that
     * alter the underlying state of the project in a way that might affect how future command will run.
     * @return array
     */
    public function updatedArguments(): array
    {
        return $this->args;
    }

    /**
     * Add some extra arguments to the command to get it to run properly in automated mode and filter out any unneeded
     * arguments.
     * @param array $arg
     * @return array
     */
    protected abstract function enrichArgs(array $arg): array;


    /**
     * Flag that can be read during the normal execution of the command to alter the behavior of the command.
     * @return bool
     */
    public function isAutomated(): bool
    {
        return $this->isBeingRunAutomated;
    }

    /**
     * Setter for the Code Change set for this automated command. Commands using this trait should call this method so
     * the parent CommandRunner can no what has changed.
     * @param CodeChangeSet $diff
     * @return void
     */
    protected function setDiff(CodeChangeSet $diff): void
    {
        $this->diff = $diff;
    }
}
