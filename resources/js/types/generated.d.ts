declare namespace App.Data.Cleared {
  export type ClearedStudentData = {
    id: number;
    name: string;
    registrationNumber: string;
    gender: App.Enums.Gender;
    status: App.Enums.StudentStatus;
    fcgpa: string;
    dateCleared: string;
    slug: string;
  };
  export type ClearedStudentListData = {
    faculty: App.Data.Faculty.FacultyData;
    department: App.Data.Department.DepartmentData;
    data: Array<App.Data.Cleared.ClearedStudentData>;
  };
}
declare namespace App.Data.Composite {
  export type CompositeCourseData = {
    code: string;
    grade: string;
    score: string;
  };
  export type CompositeCourseListData = {
    code: string;
    unit: number;
  };
  export type CompositeRowData = {
    studentId: number;
    studentName: string;
    registrationNumber: string;
    creditUnitTotal: string;
    gradePointTotal: string;
    gradePointAverage: string;
    levelCourses: Array<App.Data.Composite.CompositeCourseData>;
    otherCourses: string;
    remark: string;
  };
  export type CompositeSheetData = {
    program: App.Data.Program.ProgramData;
    faculty: App.Data.Faculty.FacultyData;
    session: App.Data.Session.SessionData;
    semester: App.Data.Semester.SemesterData;
    level: App.Data.Level.LevelData;
    courses: Array<App.Data.Composite.CompositeCourseListData>;
    students: Array<App.Data.Composite.CompositeRowData>;
    hasOtherCourses: boolean;
  };
}
declare namespace App.Data.Course {
  export type CourseData = {
    id: number;
    name: string;
    slug: string;
  };
  export type CourseListData = {
    courses: Array<App.Data.Course.CourseData>;
  };
}
declare namespace App.Data.Department {
  export type DepartmentData = {
    id: number;
    name: string;
    slug: string;
  };
  export type DepartmentListData = {
    data: Array<App.Data.Department.DepartmentData>;
  };
}
declare namespace App.Data.Download {
  export type PortalCourseData = {
    onlineId: string;
    code: string;
    title: string;
  };
  export type PortalDepartmentData = {
    onlineId: string;
    departmentCode: string;
    departmentName: string;
    facultyName: string;
    programs: Array<App.Data.Download.PortalProgramData>;
  };
  export type PortalProgramData = {
    name: string;
  };
  export type PortalRegistrationData = {
    onlineId: string;
    registrationNumber: string;
    session: string;
    semester: string;
    level: string;
    courseId: string;
    creditUnit: string;
    registrationDate: string | null;
    source: App.Enums.RecordSource;
  };
  export type PortalResultData = {
    onlineId: string;
    courseRegistrationId: string;
    registrationNumber: string;
    inCourseScore: string;
    examScore: string;
    totalScore: string;
    grade: string;
    uploadDate: string;
    source: App.Enums.RecordSource;
    examDate: string;
    lecturerName: string;
    lecturerEmail: string;
    lecturerPhoneNumber: string;
    lecturerDepartment: string;
  };
  export type PortalStudentData = {
    onlineId: string;
    lastName: string;
    firstName: string;
    otherNames: string;
    registrationNumber: string;
    gender: string;
    dateOfBirth: string;
    departmentId: string;
    option: string;
    state: string;
    localGovernment: string;
    entrySession: string;
    entryMode: string;
    entryLevel: string;
    jambRegistrationNumber: string;
    email: string;
    phoneNumber: string;
  };
}
declare namespace App.Data.Faculty {
  export type FacultyData = {
    id: number;
    name: string;
    slug: string;
  };
}
declare namespace App.Data.Import {
  export type ImportEventData = {
    id: number;
    target: string;
    type: App.Enums.ImportEventType;
    content: string;
    description: string;
    status: App.Enums.ImportEventStatus;
    date: string;
  };
  export type PendingImportEventData = {
    id: number;
    type: App.Enums.ImportEventType;
    content: string;
    date: string;
    width: number;
    elements: { [key: number]: App.Enums.ImportEventStatus };
    status: App.Enums.ImportEventStatus;
    canBeContinued: boolean;
  };
}
declare namespace App.Data.Level {
  export type LevelData = {
    id: number;
    name: string;
    slug: string;
  };
  export type LevelListData = {
    levels: Array<App.Data.Level.LevelData>;
  };
}
declare namespace App.Data.Program {
  export type ProgramData = {
    id: number;
    name: string;
    slug: string;
  };
  export type ProgramListData = {
    programs: Array<App.Data.Program.ProgramData>;
  };
}
declare namespace App.Data.Query {
  export type ProgramCoursesData = {
    curriculumId: number;
    programCourseId: number;
    sessionId: number;
    session: string;
    levelId: number;
    level: string;
    semester: string;
    courseId: number;
    courseCode: string;
    courseTitle: string;
    creditUnit: App.Enums.CreditUnit;
    courseType: App.Enums.CourseType;
    minElectiveUnit: number;
    minElectiveCount: number;
  };
  export type StudentCoursesData = {
    studentId: number;
    registrationId: number;
    sessionId: number;
    session: string;
    semester: string;
    courseId: number;
    courseCode: string;
    courseTitle: string;
    creditUnit: App.Enums.CreditUnit;
    courseStatus: App.Enums.CourseStatus;
    totalScore: number | null;
    grade: App.Enums.Grade | null;
    gradePoint: number | null;
    programCurriculumCourseId: number | null;
  };
}
declare namespace App.Data.Response {
  export type ResponseData = {
    key: string;
    message: string | boolean;
  };
}
declare namespace App.Data.Results {
  export type GradingSchemeData = {
    range: string;
    interpretation: string;
    grade: string;
    gradePoint: number;
  };
  export type ResultData = {
    id: number;
    courseCode: string;
    courseTitle: string;
    creditUnit: number;
    totalScore: number;
    grade: string;
    gradePoint: number;
    remark: string | null;
  };
  export type SemesterResultData = {
    id: number;
    results: Array<App.Data.Results.ResultData>;
    semester: string;
    creditUnitTotal: number;
    gradePointTotal: number;
    gradePointAverage: number;
    formattedCreditUnitTotal: string;
    formattedGradePointTotal: string;
    formattedGPA: string;
  };
  export type SessionResultData = {
    id: number;
    semesterResults: Array<App.Data.Results.SemesterResultData>;
    session: string;
    year: string;
    cumulativeGradePointAverage: number;
    formattedCGPA: string;
  };
  export type StudentResultData = {
    id: number;
    sessionEnrollments: Array<App.Data.Results.SessionResultData>;
    finalCumulativeGradePointAverage: number;
    formattedFCGPA: string;
    degreeClass: string;
    degreeAwarded: string;
    graduationYear: number;
  };
  export type TranscriptData = {
    recordsUnitHead: string;
    gradingSchemes: Array<App.Data.Results.GradingSchemeData>;
  };
}
declare namespace App.Data.Semester {
  export type SemesterData = {
    id: number;
    name: string;
    slug: string;
  };
  export type SemesterListData = {
    semesters: Array<App.Data.Semester.SemesterData>;
  };
}
declare namespace App.Data.Session {
  export type SessionData = {
    id: number;
    name: string;
    slug: string;
  };
  export type SessionListData = {
    sessions: Array<App.Data.Session.SessionData>;
  };
}
declare namespace App.Data.Shared {
  export type NotificationData = {
    type: App.Enums.NotificationType;
    body: string;
  };
  export type SharedData = {
    user: App.Data.Shared.UserData;
    notification: App.Data.Shared.NotificationData | null;
  };
  export type UserData = {
    id: number;
    name: string;
    email: string;
  };
}
declare namespace App.Data.Students {
  export type StudentComprehensiveData = {
    basic: App.Data.Students.StudentData;
  };
  export type StudentData = {
    id: number;
    registrationNumber: string;
    lastName: string;
    firstName: string;
    otherNames: string | null;
    name: string;
    gender: App.Enums.Gender;
    birthDate: string;
    program: string;
    department: string;
    faculty: string;
    admissionYear: number;
    nationality: string;
    slug: string;
    status: App.Enums.StudentStatus;
  };
  export type StudentListPaginatedData = {
    students: any;
  };
}
declare namespace App.Data.Summary {
  export type DepartmentResultSummaryData = {
    department: App.Data.Department.DepartmentData;
    session: App.Data.Session.SessionData;
    level: App.Data.Level.LevelData;
    students: Array<App.Data.Summary.StudentResultSummaryData>;
  };
  export type StudentResultSummaryData = {
    student: App.Data.Students.StudentData;
    fcgpa: number;
  };
}
declare namespace App.Data.Vetting {
  export type VettingListData = {
    faculty: App.Data.Faculty.FacultyData;
    department: App.Data.Department.DepartmentData;
    graduands: Array<App.Data.Vetting.VettingStudentData>;
  };
  export type VettingReportData = {
    id: number;
    vettableId: number;
    vettableType: string;
    message: string;
  };
  export type VettingStepData = {
    id: number;
    type: App.Enums.VettingType;
    title: string;
    description: string;
    status: App.Enums.VettingStatus;
    color: App.Enums.StatusColor;
    reports: Array<App.Data.Vetting.VettingReportData>;
  };
  export type VettingStepListData = {
    items: Array<App.Data.Vetting.VettingStepData>;
  };
  export type VettingStudentData = {
    id: number;
    name: string;
    registrationNumber: string;
    studentStatus: App.Enums.StudentStatus;
    vettingStatus: App.Enums.VettingEventStatus;
    vettingStatusColor: App.Enums.StatusColor;
    slug: string;
  };
}
declare namespace App.Enums {
  export type ClassOfDegree =
    | "FIRST CLASS HONOURS"
    | "SECOND CLASS HONOURS (UPPER DIVISION)"
    | "SECOND CLASS HONOURS (LOWER DIVISION)"
    | "THIRD CLASS HONOURS"
    | "PASS"
    | "FAIL";
  export type CourseStatus = "F" | "R";
  export type CourseType = "C" | "E" | "G" | "A" | "R";
  export type CreditUnit = 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | 12 | 15 | 18 | 24;
  export type CumulativeComputationStrategy = "semester" | "universal";
  export type EntryMode = "UTME" | "DENT" | "PD" | "TRAN";
  export type Gender = "M" | "F";
  export type Grade = "A" | "B" | "C" | "D" | "E" | "F";
  export type ImportEventMethod =
    | "all"
    | "course"
    | "department"
    | "department_session"
    | "department_session_level"
    | "department_session_semester"
    | "registration_number"
    | "registration_number_session_semester"
    | "session"
    | "session_course";
  export type ImportEventStatus =
    | "new"
    | "queued"
    | "started"
    | "downloading"
    | "downloaded"
    | "saving"
    | "saved"
    | "processing"
    | "cancelled"
    | "failed"
    | "completed";
  export type ImportEventType = "results" | "courses" | "departments" | "students" | "registrations";
  export type LevelEnum = "100" | "200" | "300" | "400" | "500" | "600";
  export type NotificationType = "success" | "error" | "warning" | "info";
  export type ProgramDuration = 3 | 4 | 5 | 6;
  export type RawDataStatus = "pending" | "duplicate" | "failed" | "processed";
  export type RecordSource = "portal" | "excel" | "legacy";
  export type ResultRemark = "PAS" | "FAL" | "ABS" | "MAL";
  export type Role = "super-admin" | "admin" | "desk-officer" | "exam-officer" | "database-officer" | "user";
  export type ScoreType = "course_work" | "exam";
  export type StatusColor = "gray" | "red" | "yellow" | "green" | "blue" | "purple" | "indigo" | "pink";
  export type StudentStatus =
    | "new"
    | "active"
    | "inactive"
    | "probation"
    | "withdrawn"
    | "expelled"
    | "suspended"
    | "deceased"
    | "transferred"
    | "final"
    | "extra"
    | "cleared"
    | "graduated";
  export type VettingEventStatus = "new" | "pending" | "vetting" | "failed" | "passed";
  export type VettingStatus = "new" | "checking" | "unchecked" | "failed" | "passed";
  export type VettingType =
    | "organize_year"
    | "validate_result"
    | "semester_credit"
    | "match_courses"
    | "credit_units"
    | "first_year"
    | "core_courses"
    | "elective_courses"
    | "failed_courses";
  export type Year = 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | 12;
}
declare namespace App.ViewModels.Downloads {
  export type DownloadCoursesPage = {
    events: any;
    pending: App.Data.Import.PendingImportEventData;
  };
  export type DownloadRegistrationPage = {
    departments: App.Data.Department.DepartmentListData;
    sessions: App.Data.Session.SessionListData;
    semesters: App.Data.Semester.SemesterListData;
    courses: App.Data.Course.CourseListData;
    levels: App.Data.Level.LevelListData;
    events: any;
    pending: App.Data.Import.PendingImportEventData;
    selectedIndex: number;
  };
  export type DownloadStudentPage = {
    department: App.Data.Department.DepartmentListData;
    session: App.Data.Session.SessionListData;
    events: any;
    pending: App.Data.Import.PendingImportEventData;
    selectedIndex: number;
  };
}
declare namespace App.ViewModels.Reports {
  export type ClearedIndexPageData = {
    departments: App.Data.Department.DepartmentListData;
    students: App.Data.Cleared.ClearedStudentListData;
  };
  export type CompositeFormPage = {
    program: App.Data.Program.ProgramListData;
    semester: App.Data.Semester.SemesterListData;
    session: App.Data.Session.SessionListData;
    level: App.Data.Level.LevelListData;
  };
  export type CompositeViewPage = {
    data: App.Data.Composite.CompositeSheetData;
  };
}
declare namespace App.ViewModels.Results {
  export type ResultViewPage = {
    student: App.Data.Students.StudentData;
    results: App.Data.Results.StudentResultData;
  };
}
declare namespace App.ViewModels.Students {
  export type StudentIndexPage = {
    data: App.Data.Students.StudentListPaginatedData;
  };
  export type StudentShowPage = {
    student: App.Data.Students.StudentComprehensiveData;
    selectedIndex: number;
  };
}
declare namespace App.ViewModels.Summary {
  export type SummaryFormPage = {
    department: App.Data.Department.DepartmentListData;
    session: App.Data.Session.SessionListData;
    level: App.Data.Level.LevelListData;
  };
  export type SummaryViewPage = {
    department: App.Data.Summary.DepartmentResultSummaryData;
  };
}
declare namespace App.ViewModels.Vetting {
  export type VettingIndexPage = {
    departments: App.Data.Department.DepartmentListData;
    data: App.Data.Vetting.VettingListData;
    steps: App.Data.Vetting.VettingStepListData;
  };
}
