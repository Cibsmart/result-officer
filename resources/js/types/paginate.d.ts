export interface PaginationInterface {
  currentPage: number;
  data: Array<any>;
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: Array<PaginationLink>;
  next_page_url: string;
  path: string;
  per_page: number;
  prev_page_url: string;
  to: number;
  total: number;
}

export type PaginationLink = {
  active: boolean;
  label: string;
  url: string;
};

export interface PaginatedStudentListData extends PaginationInterface {
  data: Array<App.Data.Students.StudentBasicData>;
}
