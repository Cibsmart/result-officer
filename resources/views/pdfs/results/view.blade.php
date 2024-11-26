<html lang="en">
<head>
  <title>{{ "$student->registrationNumber - EBSU RESULT DATABASE OFFICE - " . auth()->id() }}</title>
  <link rel="stylesheet" href="{{ asset("css/prints.css") }}">
</head>
<body>

<div class="mt-4">
  <div class="max-w-2xl mx-auto">
    <div class="flex flex-row items-baseline">
      {{--      Logo --}}
      <div class="basis-1/6 text-center text-gray-300"></div>
      <div class="basis-2/3">
        <div class="flex flex-col text-center">
          <span class="text-black text-lg font-bold">EBONYI STATE UNIVERSITY, ABAKALIKI</span>
          <span class="text-black text-sm  font-bold">RESULT DATABASE OFFICE</span>
          <span class="text-black  text-sm font-bold">STUDENT'S RESULT (DRAFT COPY)</span>
        </div>
      </div>
      {{-- Passport --}}
      <div class="basis-1/6 text-center text-gray-300"></div>
    </div>
  </div>

  <div class="mt-2 max-w-xl mx-auto">
    <table class="min-w-full mx-auto divide-y divide-gray-400 ring-1 ring-gray-400">
      <thead>
      <tr class="divide-x divide-gray-400">
        <th scope="col" class="p-1.5 text-left text-xs font-semibold text-gray-900 uppercase">
          Name: {{ $student->name }}
        </th>
      </tr>
      </thead>
      <tbody class="divide-y divide-gray-400 bg-white">
      <tr class="divide-x divide-gray-400">
        <td class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 font-semibold uppercase">
          Registration Number: {{ $student->registrationNumber }}
        </td>
      </tr>

      <tr class="divide-x divide-gray-400">
        <td class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 font-semibold uppercase">
          Department: {{ $student->department }}
        </td>
      </tr>
      </tbody>
    </table>
  </div>

  <table class="max-w-7xl mt-4 mx-auto divide-y divide-gray-400 ring-1 ring-gray-400">
    <thead>
    <tr class="divide-x divide-gray-400">
      <th scope="col" class="py-1 px-2 text-center text-xs font-semibold text-gray-900">SN</th>
      <th scope="col" class="px-2 py-1 text-left text-xs font-semibold text-gray-900"> Code</th>
      <th scope="col" class="px-2 py-1 text-left text-xs font-semibold text-gray-900">Course Title</th>
      <th scope="col" class="px-2 py-1 text-center text-xs font-semibold text-gray-900">Unit</th>
      <th scope="col" class="px-2 py-1 text-center text-xs font-semibold text-gray-900">Score</th>
      <th scope="col" class="px-2 py-1 text-center text-xs font-semibold text-gray-900">Grade</th>
      <th scope="col" class="px-2 py-1 text-center text-xs font-semibold text-gray-900">GP</th>
      <th scope="col" class="px-2 py-1 text-center text-xs font-semibold text-gray-900">GPA</th>
      <th scope="col" class="py-1 px-2 text-center text-xs font-semibold text-gray-900">CGPA</th>
    </tr>
    </thead>
    <tbody class="divide-y divide-gray-400 bg-white">
    @foreach($results->sessionEnrollments as $session)
      @foreach($session->semesterResults as $semester)
        <tr class="divide-x divide-gray-400">
          <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-extrabold" colspan="9">
            {{ $session->session }} {{ $semester->semester }} SEMESTER
          </td>
        </tr>

        @foreach($semester->results as $index => $result)
          <tr class="divide-x divide-gray-400">
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $index + 1 }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-gray-900">{{ $result->courseCode }}</td>
            <td class="p-1 text-xs text-gray-900">{{ $result->courseTitle }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->creditUnit }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->totalScore }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->grade }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $result->gradePoint }}</td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>
            <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900"></td>
          </tr>
        @endforeach

        <tr class="divide-x divide-gray-400">
          <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900" colspan="3"></td>
          <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">{{ $semester->formattedCreditUnitTotal }}</td>
          <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900" colspan="2"></td>
          <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">{{ $semester->formattedGradePointTotal }}</td>
          <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">{{ $semester->formattedGPA }}</td>
          <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900 font-bold">
            @if($loop->last)
              {{ $session->formattedCGPA }}
            @endif
          </td>
        </tr>
      @endforeach
    @endforeach
    </tbody>
  </table>

  {{-- FCGPA  --}}
  <div class="mt-2 p-2 text-center text-sm font-bold uppercase text-black">
    CURRENT FINAL CGPA: {{ $results->formattedFCGPA }} ({{ $results->degreeClass }})
  </div>
</div>

@include('pdfs.shared.signature')

</body>
</html>
