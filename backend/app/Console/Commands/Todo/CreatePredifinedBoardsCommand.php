<?php

namespace App\Console\Commands\Todo;

use App\Factories\Todo\PredefinedBoardFactory;
use App\Models\Board;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class CreatePredifinedBoardsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todo:create-predefined-boards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will create predefined boards on each users.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->savePredefinedBoardsOnUsers();
        $this->savePredefinedBoardOnCompanies();

        return Command::SUCCESS;
    }

    protected function savePredefinedBoardOnCompanies()
    {
        $this->info('Creating predefined boards on companies...');

        $this->withProgressBar(
            Company::with('user')->get(),
            function (Company $company) {
                if (Board::query()
                    ->predefined()
                    ->whereBelongsTo($company, 'team')
                    ->doesntExist()
                ) {
                    PredefinedBoardFactory::make($company->user, $company);
                }

                // Add employees to the Company Board
                Board::query()
                    ->predefined()
                    ->whereHas('team', fn (Builder $query) => $query->whereKey($company->getKey()))
                    ->get()
                    ->each(function (Board $board) use ($company) {
                        if (
                            $board->subscribers()->count() == 1
                            && $company->employees()->count() == 1
                        ) {
                            return;
                        }

                        // Add employees to this Company board
                        $company->employees()
                            ->get()
                            ->each(function ($employee) use ($board) {
                                $user = $employee->user;

                                if ($board->hasUser($user)) {
                                    return;
                                }

                                $board->addSubscriber($user);
                            });
                    });
            },
        );

        $this->info('Done creating predefined boards on companies.');
    }

    protected function savePredefinedBoardsOnUsers()
    {
        $this->info('Creating predefined boards on each users...');

        $this->withProgressBar(
            User::all(),
            function (User $user) {
                if ($user->subscribingToBoards()
                    ->boardOwner()
                    ->whereHas('board', function (Builder $query) {
                        $query->predefined();
                    })
                    ->doesntExist()
                ) {

                    PredefinedBoardFactory::make($user);
                }
            },
        );

        $this->info('Done creating predefined boards on Users.');
    }
}
