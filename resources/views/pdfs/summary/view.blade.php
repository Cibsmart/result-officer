<html lang="en">
<head>
  <title>{{ "{$summary->department->name} - EBSU RESULT DATABASE OFFICE - " . auth()->id() }}</title>
  @vite(['resources/css/app.css'])
</head>
<body>

<div class="mt-4 max-w-2xl mx-auto">
  <div class="w-full mx-auto">
    <div class="flex flex-col text-center">
      <span class="text-black text-lg font-bold">EBONYI STATE UNIVERSITY, ABAKALIKI</span>
      <span class="text-black text-sm  font-bold">RESULT DATABASE OFFICE</span>
      <span class="text-black  text-sm font-bold">DEPARTMENT RESULT SUMMARY</span>
    </div>
  </div>

  <div class="mt-2 max-w-lg mx-auto">
    <table class="min-w-full mx-auto divide-y divide-gray-400 ring-1 ring-gray-400">
      <thead>
      <tr class="divide-x divide-gray-400">
        <td colspan="2" class="p-1.5 text-left text-xs text-gray-900 uppercase">
          DEPARTMENT: <span class="font-semibold">{{ $summary->department->name }}</span>
        </td>
      </tr>
      </thead>
      <tbody class="divide-y divide-gray-400 bg-white">
      <tr class="divide-x divide-gray-400">
        <td class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 uppercase">
          SESSION: <span class="font-semibold">{{ $summary->session->name }}</span>
        </td>

        <td class="whitespace-nowrap p-1.5 text-xs text-left text-gray-900 uppercase">
          LEVEL: <span class="font-semibold">{{ $summary->level->name }}</span>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

  <table class="w-full mt-4 mx-auto divide-y divide-gray-400 ring-1 ring-gray-400">
    <thead>
    <tr class="divide-x divide-gray-400">
      <th scope="col" class="p-1 text-center text-xs font-semibold text-gray-900">SN</th>
      <th scope="col" class="p-1 text-left text-xs font-semibold text-gray-900"> STUDENTS' NAME</th>
      <th scope="col" class="p-1 text-left text-xs font-semibold text-gray-900">REGISTRATION NUMBER</th>
      <th scope="col" class="p-1 text-center text-xs font-semibold text-gray-900">CGPA</th>
    </tr>
    </thead>
    <tbody class="divide-y divide-gray-400 bg-white">
    @foreach($summary->students as $index => $student)
      <tr class="divide-x divide-gray-400">
        <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $index + 1 }}</td>
        <td class="whitespace-nowrap p-1 text-xs text-gray-900">{{ $student->student->name }}</td>
        <td class="p-1 text-xs text-gray-900">{{ $student->student->registrationNumber }}</td>
        <td class="whitespace-nowrap p-1 text-xs text-center text-gray-900">{{ $student->fcgpa }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>

@include('pdfs.shared.signature')

</body>
</html>
