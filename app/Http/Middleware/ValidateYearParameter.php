<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ValidateYearParameter
{
    /**
     * Handle an incoming request.
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $year = $request->route('year');

        if ($year !== null && !preg_match('/^20\d{2}$/', $year)) {
            abort(404, 'Invalid Year');
        }

        return $next($request);
    }
}
