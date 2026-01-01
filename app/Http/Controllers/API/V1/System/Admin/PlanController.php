<?php

namespace App\Http\Controllers\API\V1\System\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\StorePlanRequest;
use App\Http\Requests\System\UpdatePlanRequest;
use App\Http\Resources\System\PlanResource;
use App\Models\Plan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Cache::remember(
            'plans',
            now()->addMinutes(10),
            fn() =>
            Plan::all()
        );
        return success('All plans', 200, PlanResource::collection($plans));
    }
    public function store(StorePlanRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $request->filled('slug') ? Str::slug($request->slug) : generateSlug($request->name, new Plan());
        $plan = Plan::create($data);
        return success('New plan added successfully', 201, PlanResource::make($plan));
    }
    public function show(string $slug)
    {
        $plan = Cache::remember(
            "plan.{$slug}",
            now()->addMinutes(10),
            fn() => Plan::where('slug', $slug)->firstOrFail()
        );
        return success('Plan data', 200, $plan);
    }
    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $data = $request->validated();
        if ($request->filled('name') || $request->filled('slug')) {
            $data['slug'] = $request->filled('slug') ? Str::slug($request->slug) : generateSlug($request->name, $plan);
        }
        $plan->update($data);
        return success('Plan updated successfully', 200, PlanResource::make($plan));
    }
    public function toggleActive(Plan $plan)
    {
        $plan->update([
            'is_active' => ! $plan->is_active
        ]);
        return success('Toggle active successfully', 200, PlanResource::make($plan));
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return success('Plan deleted successfully', 200);
    }
}
