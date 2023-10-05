<?php

namespace App\Http\Controllers;

use App\Charts\LastCommits;
use App\Models\Commits;
use App\Models\Repository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Throwable;

class RepositoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $repositories = collect();
            $response = Http::withToken($user->github_token)->get('https://api.github.com/user/repos')->json();

            foreach ($response as $repo) {
                $repoInstance = Repository::make([
                    "github_id" => $repo["id"],
                    "name" => $repo["name"],
                    "owner" => $repo["owner"]["login"],
                    "url" => $repo["html_url"],
                ]);

                $repositories->add($repoInstance);
            }

            if (isset($request->repo)) {
                $repositories = $repositories->filter(function ($repo) use ($request) {
                    return str_contains($repo->name, $request->repo);
                });
            }

            $repositories = $repositories->paginate(12);

            return view('repository.index', compact('repositories'));
        } catch (Throwable $th) {
            return Redirect::to('/dashboard')->withErrors(['Ops! Something went wrong while fetching your repositories. Please, try again later.']);
        }
    }

    public function show($owner, $repository, Request $request)
    {
        try {
            $repo = $this->fetchRepositoryToDb($owner, $repository);

            $selectedDate = $request->start;
            $commitsSummary = [];
            $daysToSubtract = 90;

            if (isset($request->start)) {
                $datePickerDate = Carbon::createFromFormat("d/m/Y", $request->start);
                $daysToSubtract = Carbon::today()->gt($datePickerDate) ? Carbon::today()->diffInDays($datePickerDate) : 0;
            }

            $this->fetchCommitsToDb($repo, $daysToSubtract);

            $commitsDateCounter = array_count_values(
                $repo->commits()
                    ->get()
                    ->pluck('date')
                    ->toArray()
            );

            $period = CarbonPeriod::create(Carbon::today()->subDays($daysToSubtract), Carbon::today());

            $rangeOfDays = collect();

            foreach ($period as $date) {
                $rangeOfDays->add($date->format('d / m / y'));

                if (isset($commitsDateCounter[$date->format('Y-m-d')])) {
                    array_push($commitsSummary, $commitsDateCounter[$date->format('Y-m-d')]);
                    continue;
                }

                array_push($commitsSummary, 0);
            }

            $chart = $this->buildChart(
                $rangeOfDays,
                $commitsSummary,
                $daysToSubtract
            );

            return view('repository.show', compact('repo', 'chart', 'selectedDate'));
        } catch (Throwable $th) {
            dd($th->getMessage());
            return Redirect::to('/repos')->withErrors(['Ops! Something went wrong while loading your repository. Please, try again later.']);
        }
    }

    protected function fetchCommitsToDb(Repository $repo, int $timeInterval, int $perPage = 100): void
    {
        $user = Auth::user();
        $since = Carbon::now()->subDays($timeInterval)->toIso8601String();

        $response = Http::withToken($user->github_token)
            ->get("https://api.github.com/repos/$repo->owner/$repo->name/commits", [
                'since' => $since,
                'per_page' => $perPage,
            ])->json();

        foreach ($response as $commit) {
            Commits::updateOrCreate([
                'sha' => $commit["sha"]
            ], [
                'repository_id' => $repo->id,
                'date' => Carbon::parse($commit['commit']['author']['date'])->format('Y-m-d')
            ]);
        }
    }

    protected function fetchRepositoryToDb(string $owner, string $repository): Repository
    {
        $user = Auth::user();
        $response = Http::withToken($user->github_token)->get("https://api.github.com/repos/$owner/$repository")->json();

        $repo = Repository::updateOrCreate([
            'github_id' => $response['id'],
        ], [
            'name' => $response['name'],
            'owner' => $response['owner']['login'],
            'url' => $response['html_url'],
        ]);

        return $repo;
    }

    protected function buildChart(
        Collection $rangeOfDays,
        array $commitsSummary,
        int $daysToSubtract
    ): LastCommits {
        $chart = new LastCommits;

        $chart->labels($rangeOfDays);
        $totalCommits = array_sum($commitsSummary);
        $dataset = $chart->dataset(
            "A total of {$totalCommits} commits in {$daysToSubtract} days",
            'line',
            $commitsSummary
        );
        $dataset->backgroundColor('#ffffff2e');

        return $chart;
    }
}
