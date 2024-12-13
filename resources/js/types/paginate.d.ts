export interface PaginationInterface {
  currentPage: number;
  data: Array<any>;
  firstPageUrl: string;
  from: number | null;
  lastPage: number;
  lastPageUrl: string;
  links: Array<PaginationLink>;
  nextPageUrl: string | null;
  path: string;
  perPage: string;
  previousPageUrl: string | null;
  to: number | null;
  totalPages: number;
}

export type PaginationLink = {
  active: boolean;
  label: string;
  url: string | null;
};

export interface PaginatedStudentListData extends PaginationInterface {
  data: Array<App.Data.Students.StudentData>;
}
