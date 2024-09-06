<html lang="en">
<head>
  <title>{{ "COMPOSITE SHEET - {$data->program->name}-{$data->session->name}-{$data->semester->name}-{$data->level->name} "}}</title>
  @vite(['resources/css/app.css'])
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

  {{--  @foreach($results->enrollments as $session)--}}
  {{--    @foreach($session->semesterResults as $semester)--}}
  {{--      <table class="w-full mt-4 mx-auto divide-y divide-gray-400 ring-1 ring-gray-400">--}}
  {{--        <thead>--}}
  {{--        <tr class="divide-x divide-gray-400">--}}
  {{--          <th scope="col" class="p-1 text-center text-xs font-semibold text-gray-900 w-18">YEAR</th>--}}
  {{--          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-18">SEMESTER</th>--}}
  {{--          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-16">COURSE CODE</th>--}}
  {{--          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-96">COURSE TITLE</th>--}}
  {{--          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">CREDIT HOUR</th>--}}
  {{--          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">LETTER GRADE</th>--}}
  {{--          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">GRADE POINT</th>--}}
  {{--          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">GPA</th>--}}
  {{--          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">CGPA</th>--}}
  {{--          <th scope="col" class="px-2 py-1 text-center text-xs font-medium text-gray-900 w-12">FCGPA</th>--}}
  {{--        </tr>--}}
  {{--        </thead>--}}

  {{--        @foreach($semester->results as $result)--}}
  {{--          <tbody class="divide-y divide-gray-400 bg-white">--}}

  {{--          <tr class="divide-x divide-gray-400">--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">--}}
  {{--              @if($loop->first)--}}
  {{--                {{ $session->year }}--}}
  {{--              @endif--}}
  {{--            </td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-gray-900">--}}
  {{--              @if($loop->first)--}}
  {{--                {{ $semester->semester }}--}}
  {{--              @endif--}}
  {{--            </td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-gray-900">{{ $result->courseCode }}</td>--}}
  {{--            <td class="p-1 text-xs text-gray-900">{{ $result->courseTitle }}</td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->creditUnit }}</td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->grade }}</td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->gradePoint }}</td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>--}}
  {{--          </tr>--}}

  {{--          @endforeach--}}
  {{--          <tr class="divide-x divide-gray-400">--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900" colspan="4"></td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">{{ $semester->creditUnitTotal }}</td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">{{ $semester->gradePointTotal }}</td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">{{ $semester->gradePointAverage }}</td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">--}}
  {{--              @if($loop->last)--}}
  {{--                {{ $session->cumulativeGradePointAverage }}--}}
  {{--              @endif--}}
  {{--            </td>--}}
  {{--            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">--}}
  {{--              @if($loop->parent->last && $loop->last)--}}
  {{--                {{ $results->finalCumulativeGradePointAverage }}--}}
  {{--              @endif--}}
  {{--            </td>--}}
  {{--          </tr>--}}
  {{--          </tbody>--}}
  {{--      </table>--}}
  {{--    @endforeach--}}

  {{--    <div class="flex w-full justify-between mx-auto space-x-4">--}}
  {{--      <div class="basis-4/12">--}}
  {{--        <table class="w-full mt-4 mx-auto divide-y divide-gray-400 ring-1 ring-gray-400 text-gray-700">--}}
  {{--          <thead>--}}
  {{--          <tr class="divide-x divide-gray-400">--}}
  {{--            <th scope="col" class="p-1 text-center text-xs font-semibold text-gray-900">%</th>--}}
  {{--            <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900">INTERPRETATION</th>--}}
  {{--            <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900">LETTER</th>--}}
  {{--            <th scope="col" class="px-2 py-1 text-center text-xs font-medium text-gray-900">POINT</th>--}}
  {{--          </tr>--}}
  {{--          </thead>--}}

  {{--          <tbody class="divide-y divide-gray-400 bg-white">--}}
  {{--          @foreach($transcript->gradingSchemes as $item)--}}
  {{--            <tr class="divide-x divide-gray-400">--}}
  {{--              <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $item->range }}</td>--}}
  {{--              <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $item->interpretation }}</td>--}}
  {{--              <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $item->grade }}</td>--}}
  {{--              <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $item->gradePoint }}</td>--}}
  {{--            </tr>--}}
  {{--          @endforeach--}}
  {{--          </tbody>--}}
  {{--        </table>--}}
  {{--      </div>--}}
  {{--      <div class="basis-2/12 mt-4">--}}
  {{--        <div class="h-full grid place-content-center font-extrabold">--}}
  {{--          @if($loop->last)--}}
  {{--            FCGPA: {{ $results->finalCumulativeGradePointAverage }}--}}
  {{--          @endif--}}
  {{--        </div>--}}
  {{--      </div>--}}
  {{--      <div class="basis-6/12">--}}
  {{--        <table class="w-full mt-4 mx-auto ring-1 ring-gray-400 text-gray-700">--}}
  {{--          <tbody class="divide-y divide-gray-400 bg-white">--}}
  {{--          <tr>--}}
  {{--            <td class="p-1 text-left text-xs text-gray-900">--}}
  {{--              DEGREE AWARDED:--}}
  {{--              <span class="font-semibold">--}}
  {{--                @if($loop->last)--}}
  {{--                  {{ $results->degreeAwarded }}--}}
  {{--                @else--}}
  {{--                  XXXXXXXXXXXXXXXXXXXXXXXXX--}}
  {{--                @endif--}}
  {{--              </span>--}}
  {{--            </td>--}}
  {{--          </tr>--}}

  {{--          <tr>--}}
  {{--            <td class="p-1 text-left text-xs text-gray-900">--}}
  {{--              CLASS:--}}
  {{--              <span class="font-semibold">--}}
  {{--                @if($loop->last)--}}
  {{--                  {{ $results->degreeClass }}--}}
  {{--                @else--}}
  {{--                  XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX--}}
  {{--                @endif--}}
  {{--              </span>--}}
  {{--            </td>--}}
  {{--          </tr>--}}

  {{--          <tr>--}}
  {{--            <td class="p-1 text-left text-xs text-gray-900">--}}
  {{--              DATE OF GRADUATION:--}}
  {{--              <span class="font-semibold">--}}
  {{--                @if($loop->last)--}}
  {{--                  {{ $results->graduationYear }}--}}
  {{--                @else--}}
  {{--                  XXXXXXXXXXXXXXXXXXXXXXX--}}
  {{--                @endif--}}
  {{--              </span>--}}
  {{--            </td>--}}
  {{--          </tr>--}}

  {{--          <tr>--}}
  {{--            <td class="p-1 text-left text-xs text-gray-900">--}}
  {{--              <div class="flex flex-col space-y-11">--}}
  {{--                <div>CERTIFIED BY:</div>--}}

  {{--                <div class="flex flex-col items-center">--}}
  {{--                  <span class="font-extrabold">{{ $transcript->recordsUnitHead }}</span>--}}
  {{--                  <span>FOR: REGISTRAR</span>--}}
  {{--                </div>--}}
  {{--              </div>--}}
  {{--            </td>--}}
  {{--          </tr>--}}
  {{--          </tbody>--}}
  {{--        </table>--}}
  {{--      </div>--}}
  {{--    </div>--}}
  {{--    @pageBreak--}}
  {{--  @endforeach--}}
</div>

</body>
</html>
