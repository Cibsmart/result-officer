<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Months;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

final class ValidateMonthParameter
{
    /**
     * Handle an incoming request.
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $month = $request->route('month');

        $validator = Validator::make(['month' => $month], [
            'month' => [Rule::enum(Months::class)],
        ]);

        if ($month !== null && $validator->fails()) {
            abort(404, 'Invalid Month');
        }

        return $next($request);
    }
}
