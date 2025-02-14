<html lang="en">
<head>
  <title>{{ "$student->registrationNumber - OFFICIAL TRANSCRIPT"}}</title>
  <link rel="stylesheet" href="{{ asset("css/prints.css") }}">
</head>
<body>

<div class="mt-4 max-w-3xl mx-auto mb-4">
  <div class="w-full mx-auto">
    <div class="flex flex-col text-center">
      <span class="text-black text-lg leading-5 font-bold">EBONYI STATE UNIVERSITY, ABAKALIKI</span>
      <span class="text-black text-md leading-5  font-bold">OFFICE OF THE REGISTRAR</span>
      <span class="text-black  text-md leading-5 font-bold">RECORDS UNIT</span>
      <span class="text-black  text-md leading-5 font-bold">TRANSCRIPT OF ACADEMIC RECORDS</span>
    </div>
  </div>

  <div class="mt-2 w-full mx-auto">
    <table class="min-w-full mx-auto divide-y divide-gray-400 ring-1 ring-gray-400">
      <tbody class="divide-y divide-gray-400 bg-white">
      <tr class="divide-x divide-gray-400">
        <td colspan="2" class="p-1.5 text-left text-xs text-gray-900 uppercase">
          <div class="flex flex-col space-y-1">
            <div>SURNAME: <span class="font-semibold">{{ $student->lastName }}</span></div>
            <div>OTHER NAMES: <span class="font-semibold">{{ "$student->firstName $student->otherNames" }}</span></div>
          </div>
        </td>

        <td colspan="2" class="p-1.5 text-left text-xs text-gray-900 uppercase">
          Registration Number: <span class="font-semibold">{{ $student->registrationNumber }}</span>
        </td>
      </tr>

      <tr class="divide-x divide-gray-400">
        <td class="p-1.5 text-left text-xs text-gray-900 uppercase w-32">
          Sex: <span class="font-semibold">{{ $student->gender }}</span>
        </td>

        <td class="p-1.5 text-left text-xs text-gray-900 uppercase w-80">
          DATE OF BIRTH: <span class="font-semibold">{{ $student->birthDate }}</span>
        </td>

        <td class="p-1.5 text-left text-xs text-gray-900 uppercase w-80">
          DATE OF ADMISSION: <span class="font-semibold">{{ $student->admissionYear }}</span>
        </td>

        <td class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 uppercase w-48">
          NATIONALITY: <span class="font-semibold">{{ $student->nationality }}</span>
        </td>
      </tr>

      <tr class="divide-x divide-gray-400">
        <td colspan="2" class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 uppercase">
          FACULTY: <span class="font-semibold">{{ $student->faculty }}</span>
        </td>
        <td colspan="2" class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 uppercase">
          DEPARTMENT: <span class="font-semibold">{{ $student->department }}</span>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

  @foreach($results->finalSessionEnrollments as $session)
    @foreach($session->finalSemesterResults as $semester)
      <table class="w-full mt-4 mx-auto divide-y divide-gray-400 ring-1 ring-gray-400">
        <thead>
        <tr class="divide-x divide-gray-400">
          <th scope="col" class="p-1 text-center text-xs font-semibold text-gray-900 w-18">YEAR</th>
          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-18">SEMESTER</th>
          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-16">COURSE CODE</th>
          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-96">COURSE TITLE</th>
          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">CREDIT HOUR</th>
          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">LETTER GRADE</th>
          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">GRADE POINT</th>
          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">GPA</th>
          <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900 w-12">CGPA</th>
          <th scope="col" class="px-2 py-1 text-center text-xs font-medium text-gray-900 w-12">FCGPA</th>
        </tr>
        </thead>

        @foreach($semester->results as $result)
          <tbody class="divide-y divide-gray-400 bg-white">

          <tr class="divide-x divide-gray-400">
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">
              @if($loop->first)
                {{ $session->year }}
              @endif
            </td>
            <td class="whitespace-nowrap p-1 text-xs text-gray-900">
              @if($loop->first)
                {{ $semester->semester }}
              @endif
            </td>
            <td class="whitespace-nowrap p-1 text-xs text-gray-900">{{ $result->courseCode }}</td>
            <td class="p-1 text-xs text-gray-900">{{ $result->courseTitle }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->creditUnit }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->grade }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->gradePoint }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>
          </tr>

          @endforeach
          <tr class="divide-x divide-gray-400">
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900" colspan="4"></td>
            <td
              class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">{{ $semester->formattedCreditUnitTotal }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>
            <td
              class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">{{ $semester->formattedGradePointTotal }}</td>
            <td
              class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">{{ $semester->formattedGPA }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">
              @if($loop->last)
                {{ $session->formattedCGPA }}
              @endif
            </td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">
              @if($loop->parent->last && $loop->last)
                {{ $results->formattedFCGPA }}
              @endif
            </td>
          </tr>
          </tbody>
      </table>
    @endforeach

    <div class="flex w-full justify-between mx-auto space-x-4">
      <div class="basis-4/12">
        <table class="w-full mt-4 mx-auto divide-y divide-gray-400 ring-1 ring-gray-400 text-gray-700">
          <thead>
          <tr class="divide-x divide-gray-400">
            <th scope="col" class="p-1 text-center text-xs font-semibold text-gray-900">%</th>
            <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900">INTERPRETATION</th>
            <th scope="col" class="p-1 text-center text-xs font-medium text-gray-900">LETTER</th>
            <th scope="col" class="px-2 py-1 text-center text-xs font-medium text-gray-900">POINT</th>
          </tr>
          </thead>

          <tbody class="divide-y divide-gray-400 bg-white">
          @foreach($transcript->gradingSchemes as $item)
            <tr class="divide-x divide-gray-400">
              <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $item->range }}</td>
              <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $item->interpretation }}</td>
              <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $item->grade }}</td>
              <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $item->gradePoint }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="basis-2/12 mt-4">
        <div class="h-full grid place-content-center font-extrabold">
          @if($loop->last)
            FCGPA: {{ $results->formattedFCGPA }}
          @endif
        </div>
      </div>
      <div class="basis-6/12">
        <table class="w-full mt-4 mx-auto ring-1 ring-gray-400 text-gray-700">
          <tbody class="divide-y divide-gray-400 bg-white">
          <tr>
            <td class="p-1 text-left text-xs text-gray-900">
              DEGREE AWARDED:
              <span class="font-semibold">
                @if($loop->last)
                  {{ $results->degreeAwarded }}
                @else
                  XXXXXXXXXXXXXXXXXXXXXXXXX
                @endif
              </span>
            </td>
          </tr>

          <tr>
            <td class="p-1 text-left text-xs text-gray-900">
              CLASS:
              <span class="font-semibold">
                @if($loop->last)
                  {{ $results->degreeClass }}
                @else
                  XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
                @endif
              </span>
            </td>
          </tr>

          <tr>
            <td class="p-1 text-left text-xs text-gray-900">
              DATE OF GRADUATION:
              <span class="font-semibold">
                @if($loop->last)
                  {{ $results->graduationYear }}
                @else
                  XXXXXXXXXXXXXXXXXXXXXXX
                @endif
              </span>
            </td>
          </tr>

          <tr>
            <td class="p-1 text-left text-xs text-gray-900">
              <div class="flex flex-col space-y-11">
                <div>CERTIFIED BY:</div>

                <div class="flex flex-col items-center">
                  <span class="font-extrabold">{{ $transcript->recordsUnitHead }}</span>
                  <span>FOR: REGISTRAR</span>
                </div>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    @pageBreak
  @endforeach
</div>

</body>
</html>
