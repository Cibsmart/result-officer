declare namespace App.Enums {
  export type GenderEnum = "M" | "F";
  export type RoleEnum = "super-admin" | "admin" | "desk-officer" | "exam-officer" | "user";
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
