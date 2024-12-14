<html lang="en">
<head>
  <title>{{ "COMPOSITE SHEET - {$data->program->name}-{$data->session->name}-{$data->semester->name}-{$data->level->name} "}}</title>
  <link rel="stylesheet" href="{{ asset("css/prints.css") }}">
</head>
<body>
<div class="mt-4 max-w-7xl mx-auto mb-4">
  <div class="w-full mx-auto">
    <div class="flex flex-col text-center">
      <span class="text-black text-lg leading-5 font-bold">EBONYI STATE UNIVERSITY, ABAKALIKI</span>
      <span class="text-black text-md leading-5  font-bold">RESULT DATABASE OFFICE</span>
      <span class="text-black  text-md leading-5 font-bold">COMPOSITE SHEET</span>
    </div>
  </div>

  <div class="mt-2 w-full mx-auto">
    <table class="min-w-full mx-auto divide-y divide-gray-400 ring-1 ring-gray-400">
      <tbody class="divide-y divide-gray-400 bg-white">
      <tr class="divide-x divide-gray-400">
        <td colspan="2" class="p-1.5 text-left text-xs text-gray-900 uppercase">
          <div class="flex flex-col space-y-1">
            <div>FACULTY: <span class="font-semibold">{{ $data->faculty->name  }}</span></div>
          </div>
        </td>

        <td colspan="2" class="p-1.5 text-left text-xs text-gray-900 uppercase">
          DEPARTMENT: <span class="font-semibold">{{ $data->program->name }}</span>
        </td>
      </tr>

      <tr class="divide-x divide-gray-400">
        <td colspan="2" class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 uppercase">
          SESSION: <span class="font-semibold">{{ $data->session->name }}</span>
        </td>
        <td class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 uppercase">
          SEMESTER: <span class="font-semibold">{{ $data->semester->name }}</span>
        </td>
        <td class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 uppercase">
          LEVEL: <span class="font-semibold">{{ $data->level->name }}</span>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

  <table class="w-full mt-4 mx-auto divide-y divide-gray-400 ring-1 ring-gray-400">
    <thead>
    <tr class="divide-x divide-gray-400">
      <th/>
      <th scope="col" colspan="2" class="p-1 text-center text-xs font-semibold text-gray-900 w-18">COURSE CODE</th>
      @foreach($data->courses as $course)
        <th scope="col" colspan="2"
            class="whitespace-nowrap p-1 text-center text-xs font-semibold text-gray-900 w-18">{{ $course->code }}</th>
      @endforeach

      @if($data->hasOtherCourses)
        <th scope="col" class="p-1 align-bottom text-center text-xs font-semibold text-gray-900 w-18">OTHER</th>
      @endif

      <th scope="col" colspan="3" class="p-1 text-center text-xs font-semibold text-gray-900 w-18">TOTALS</th>
      <th/>
    </tr>

    <tr class="divide-x divide-gray-400">
      <th scope="col" class="p-1 text-center text-xs font-semibold text-gray-900 w-18">SN</th>
      <th scope="col" class="p-1 border-t text-center text-xs font-semibold text-gray-900 w-18">NAME</th>
      <th scope="col" class="whitespace-nowrap p-1 border-t text-center text-xs font-semibold text-gray-900 w-18">
        REGISTRATION NUMBER
      </th>
      @foreach($data->courses as $course)
        <th scope="col" colspan="2"
            class="whitespace-nowrap p-1 border-t text-center text-xs font-semibold text-gray-900 w-18">{{ $course->unit }}</th>
      @endforeach

      @if($data->hasOtherCourses)
        <th scope="col" class="whitespace-nowrap p-1 text-center text-xs font-semibold text-gray-900 w-18">
          COURSES
        </th>
      @endif

      <th scope="col" class="p-1 border-t text-center text-xs font-semibold text-gray-900 w-18">TCL</th>
      <th scope="col" class="p-1 border-t text-center text-xs font-semibold text-gray-900 w-18">TGP</th>
      <th scope="col" class="p-1 border-t text-center text-xs font-semibold text-gray-900 w-18">GPA</th>
      <th scope="col" class="p-1 text-center text-xs font-semibold text-gray-900 w-18">REMARKS</th>
    </tr>
    </thead>

    <tbody class="divide-y divide-gray-400 bg-white">
    @foreach($data->students as $student)
      <tr class="divide-x divide-gray-400">
        <td class="whitespace-nowrap p-1 text-center text-xs text-gray-900">{{ $loop->index + 1  }}</td>
        <td class="whitespace-nowrap p-1 text-xs text-gray-900">{{ $student->studentName  }}</td>
        <td class="whitespace-nowrap p-1 text-center text-xs text-gray-900">{{ $student->registrationNumber  }}</td>
        @foreach($student->levelCourses as $leveCourse)
          <td class="whitespace-nowrap p-1 text-center text-xs text-gray-900">{{ $leveCourse->score  }}</td>
          <td class="whitespace-nowrap p-1 text-center text-xs text-gray-900">{{ $leveCourse->grade  }}</td>
        @endforeach

        @if($data->hasOtherCourses)
          <td class="p-1 text-left text-xs text-gray-900">{{ $student->otherCourses  }}</td>
        @endif

        <td class="whitespace-nowrap p-1 text-center text-xs text-gray-900">{{ $student->creditUnitTotal  }}</td>
        <td class="whitespace-nowrap p-1 text-center text-xs text-gray-900">{{ $student->gradePointTotal  }}</td>
        <td class="whitespace-nowrap p-1 text-center text-xs text-gray-900">{{ $student->gradePointAverage  }}</td>
        <td class="whitespace-nowrap p-1 text-left text-xs text-gray-900">{{ $student->remark  }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>

</body>
</html>
