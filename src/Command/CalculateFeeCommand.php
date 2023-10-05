<?php

namespace App\Command;

use App\Entity\Enum\LoanDuration;
use App\Entity\LoanProposal;
use App\Exception\LoanNotDivisibleBy5Exception;
use App\Exception\ValidationException\LoanProposalValidationException;
use App\Service\FeeCalculator\FeeCalculator;
use App\Service\Interpolation\LinearStrategy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'app:get-fee')]
class CalculateFeeCommand extends Command
{
    const AMOUNT = 'amount';

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly FeeCalculator      $feeCalculator,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::AMOUNT,
                InputArgument::REQUIRED,
                'Amount of the loan between 1000 and 20000 PLN; any value up to 2 decimal places');
    }

    /**
     * @throws LoanProposalValidationException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $term = $this->getLoanDuration($input, $output);
        $loanDuration = LoanDuration::tryFrom($term);
        $amount = floatval(str_replace(',', '.', $input->getArgument('amount')));

        $loanProposal = new LoanProposal($loanDuration, $amount);
        $errors = $this->validator->validate($loanProposal);

        if (count($errors) > 0) {
            throw new LoanProposalValidationException((string) $errors);
        }

        $this->feeCalculator->setInterpolationStrategy(new LinearStrategy());
        $loanFee = $this->feeCalculator->calculate($loanProposal);

        $output->write('Fee for this loan is: ' . $loanFee . PHP_EOL);

        return Command::SUCCESS;
    }

    private function getLoanDuration(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $termQuestion = new ChoiceQuestion(
            'Select term of loan',
            [12, 24],
            12
        );
        $termQuestion->setErrorMessage('Term %s is invalid.');

        return $helper->ask($input, $output, $termQuestion);
    }
}