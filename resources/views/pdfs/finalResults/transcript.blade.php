<html lang="en">
<head>
  <title>{{ "$student->registrationNumber - OFFICIAL TRANSCRIPT"}}</title>
  {{--
    Deliberately self-contained CSS rather than the Tailwind build in prints.css:
    mPDF supports neither CSS custom properties (which every --tw-* utility relies
    on) nor flexbox/grid, so this page is laid out with tables and plain CSS 2.1
    that mPDF and the browser both render the same way.
  --}}
  <style>
    body {
      color: #111827;
      font-family: sans-serif;
      font-size: 8pt;
    }

    table {
      border-collapse: collapse;
      width: 100%;
    }

    table.bordered th, table.bordered td {
      border: 0.2mm solid #9ca3af;
      padding: 1mm;
    }

    th {
      font-weight: bold;
      text-align: center;
    }

    td {
      text-align: left;
      vertical-align: top;
    }

    .heading {
      text-align: center;
    }

    .heading .institution {
      font-size: 11pt;
      font-weight: bold;
    }

    .heading .office {
      font-size: 9pt;
      font-weight: bold;
    }

    .strong {
      font-weight: bold;
    }

    .center {
      text-align: center;
    }

    .uppercase {
      text-transform: uppercase;
    }

    .spaced {
      margin-top: 3mm;
    }

    .page-break {
      page-break-after: always;
    }

    .summary td {
      border: none;
      padding: 0;
      vertical-align: top;
    }

    .summary .grading {
      padding-right: 4mm;
      width: 34%;
    }

    .summary .fcgpa {
      font-size: 9pt;
      font-weight: bold;
      padding-right: 4mm;
      text-align: center;
      vertical-align: middle;
      width: 16%;
    }

    .summary .award {
      width: 50%;
    }

    .certified {
      text-align: center;
    }
  </style>
</head>
<body>

<div class="heading">
  <div class="institution">EBONYI STATE UNIVERSITY, ABAKALIKI</div>
  <div class="office">OFFICE OF THE REGISTRAR</div>
  <div class="office">RECORDS UNIT</div>
  <div class="office">TRANSCRIPT OF ACADEMIC RECORDS</div>
</div>

<table class="bordered spaced">
  <tbody>
  <tr>
    <td colspan="2" class="uppercase">
      <div>SURNAME: <span class="strong">{{ $student->lastName }}</span></div>
      <div>OTHER NAMES: <span class="strong">{{ "$student->firstName $student->otherNames" }}</span></div>
    </td>

    <td colspan="2" class="uppercase">
      Registration Number: <span class="strong">{{ $student->registrationNumber }}</span>
    </td>
  </tr>

  <tr>
    <td class="uppercase" style="width: 15%">
      Sex: <span class="strong">{{ $student->gender }}</span>
    </td>

    <td class="uppercase" style="width: 30%">
      DATE OF BIRTH: <span class="strong">{{ $student->birthDate }}</span>
    </td>

    <td class="uppercase" style="width: 30%">
      DATE OF ADMISSION: <span class="strong">{{ $student->admissionYear }}</span>
    </td>

    <td class="uppercase" style="width: 25%">
      NATIONALITY: <span class="strong">{{ $student->nationality }}</span>
    </td>
  </tr>

  <tr>
    <td colspan="2" class="uppercase">
      FACULTY: <span class="strong">{{ $student->faculty }}</span>
    </td>
    <td colspan="2" class="uppercase">
      DEPARTMENT: <span class="strong">{{ $student->department }}</span>
    </td>
  </tr>
  </tbody>
</table>

@foreach($results->finalSessionEnrollments as $session)
  <div @class(['page-break' => ! $loop->last])>
    @foreach($session->finalSemesterResults as $semester)
      <table class="bordered spaced">
        <thead>
        <tr>
          <th style="width: 8%">YEAR</th>
          <th style="width: 9%">SEMESTER</th>
          <th style="width: 10%">COURSE CODE</th>
          <th style="width: 31%">COURSE TITLE</th>
          <th style="width: 7%">CREDIT HOUR</th>
          <th style="width: 7%">LETTER GRADE</th>
          <th style="width: 7%">GRADE POINT</th>
          <th style="width: 7%">GPA</th>
          <th style="width: 7%">CGPA</th>
          <th style="width: 7%">FCGPA</th>
        </tr>
        </thead>

        <tbody>
        @foreach($semester->results as $result)
          <tr>
            <td class="center">
              @if($loop->first)
                {{ $session->year }}
              @endif
            </td>
            <td>
              @if($loop->first)
                {{ $semester->semester }}
              @endif
            </td>
            <td>{{ $result->courseCode }}</td>
            <td>{{ $result->courseTitle }}</td>
            <td class="center">{{ $result->creditUnit }}</td>
            <td class="center">{{ $result->grade }}</td>
            <td class="center">{{ $result->gradePoint }}</td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
          </tr>
        @endforeach

        <tr>
          <td colspan="4"></td>
          <td class="center strong">{{ $semester->formattedCreditUnitTotal }}</td>
          <td class="center"></td>
          <td class="center strong">{{ $semester->formattedGradePointTotal }}</td>
          <td class="center strong">{{ $semester->formattedGPA }}</td>
          <td class="center strong">
            @if($loop->last)
              {{ $session->formattedCGPA }}
            @endif
          </td>
          <td class="center strong">
            @if($loop->parent->last && $loop->last)
              {{ $results->formattedFCGPA }}
            @endif
          </td>
        </tr>
        </tbody>
      </table>
    @endforeach

    <table class="summary spaced">
      <tr>
        <td class="grading">
          <table class="bordered">
            <thead>
            <tr>
              <th>%</th>
              <th>INTERPRETATION</th>
              <th>LETTER</th>
              <th>POINT</th>
            </tr>
            </thead>

            <tbody>
            @foreach($transcript->gradingSchemes as $item)
              <tr>
                <td class="center">{{ $item->range }}</td>
                <td class="center">{{ $item->interpretation }}</td>
                <td class="center">{{ $item->grade }}</td>
                <td class="center">{{ $item->gradePoint }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </td>

        <td class="fcgpa">
          @if($loop->last)
            FCGPA: {{ $results->formattedFCGPA }}
          @endif
        </td>

        <td class="award">
          <table class="bordered">
            <tbody>
            <tr>
              <td>
                DEGREE AWARDED:
                <span class="strong">
                  @if($loop->last)
                    {{ $results->degreeAwarded }}
                  @else
                    XXXXXXXXXXXXXXXXXXXXXXXXX
                  @endif
                </span>
              </td>
            </tr>

            <tr>
              <td>
                CLASS:
                <span class="strong">
                  @if($loop->last)
                    {{ $results->degreeClass }}
                  @else
                    XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
                  @endif
                </span>
              </td>
            </tr>

            <tr>
              <td>
                DATE OF GRADUATION:
                <span class="strong">
                  @if($loop->last)
                    {{ $results->graduationYear }}
                  @else
                    XXXXXXXXXXXXXXXXXXXXXXX
                  @endif
                </span>
              </td>
            </tr>

            <tr>
              <td>
                <div>CERTIFIED BY:</div>

                {{--
                  Blank room for a physical signature. mPDF collapses block margin
                  and padding inside a table cell and clamps line-height there, so
                  the gap has to be real blank lines to survive both renderers.
                --}}
                <div>&nbsp;<br/>&nbsp;<br/>&nbsp;</div>

                <div class="certified">
                  <div class="strong">{{ $transcript->recordsUnitHead }}</div>
                  <div>FOR: REGISTRAR</div>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </table>
  </div>
@endforeach

</body>
</html>
