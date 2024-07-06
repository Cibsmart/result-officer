declare namespace App.Data.Shared {
  export type SharedData = {
    user: App.Data.Shared.UserData;
  };
  export type UserData = {
    name: string;
    email: string;
  };
}
declare namespace App.Enums {
  export type CourseStatusEnum = "F" | "R";
  export type GenderEnum = "M" | "F";
  export type LevelEnum = 100 | 200 | 300 | 400 | 500 | 600;
  export type RoleEnum = "super-admin" | "admin" | "desk-officer" | "exam-officer" | "database-officer" | "user";
  export type ScoreTypeEnum = "course-work" | "exam";
  export type StudentStatusEnum =
    | "new"
    | "active"
    | "probation"
    | "withdrawn"
    | "expelled"
    | "suspended"
    | "deceased"
    | "transferred"
    | "graduated";
}
