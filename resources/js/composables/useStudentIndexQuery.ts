import { router } from '@inertiajs/vue3';

type StudentIndexFilterData = App.Data.Students.StudentIndexFilterData;
type StudentSortField = App.Enums.StudentSortField;

export type StudentIndexParams = Partial<Record<keyof StudentIndexFilterData, string>>;

/**
 * The student list keeps its whole state — search, filters and sort — in the query
 * string, so every view of the list is a URL that can be bookmarked or shared.
 *
 * Params are always rebuilt from the current filters rather than merged into the
 * existing URL, which drops `page` on every change: narrowing the list while sitting
 * on page 7 would otherwise land on an empty page.
 */
export function useStudentIndexQuery(filters: () => StudentIndexFilterData) {
    const params = (overrides: StudentIndexParams = {}): StudentIndexParams => {
        const current = filters();

        const merged: StudentIndexParams = {
            search: current.search,
            gender: current.gender ?? '',
            status: current.status ?? '',
            department: current.department ? String(current.department) : '',
            entrySession: current.entrySession ? String(current.entrySession) : '',
            sort: current.sort,
            direction: current.direction,
            ...overrides,
        };

        return Object.fromEntries(Object.entries(merged).filter(([, value]) => value !== ''));
    };

    /** Only the table and the filter state are refetched — the dropdown options never change. */
    const visit = (overrides: StudentIndexParams = {}): void => {
        router.get(route('students.index'), params(overrides), {
            only: ['paginated', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    };

    /** Clicking the active column flips its direction; any other column starts ascending. */
    const sortHref = (field: StudentSortField): string => {
        const current = filters();

        const direction = current.sort === field && current.direction === 'asc' ? 'desc' : 'asc';

        return route('students.index', params({ direction, sort: field }));
    };

    return { params, sortHref, visit };
}
