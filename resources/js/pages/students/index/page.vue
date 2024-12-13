<script lang="ts" setup>
import { BreadcrumbItem } from "@/types";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import { Head } from "@inertiajs/vue3";
import Card from "@/components/cards/card.vue";
import CardHeader from "@/components/cards/cardHeader.vue";
import CardFooter from "@/components/cards/cardFooter.vue";
import { PaginatedStudentListData } from "@/types/paginate";
import Pagination from "@/components/Pagination.vue";
import BaseTable from "@/components/tables/baseTable.vue";
import BaseTHead from "@/components/tables/baseTHead.vue";
import BaseTH from "@/components/tables/baseTH.vue";
import BaseTBody from "@/components/tables/baseTBody.vue";
import BaseTR from "@/components/tables/baseTR.vue";
import BaseTD from "@/components/tables/baseTD.vue";
import Badge from "@/components/badge.vue";
import SecondaryLinkSmall from "@/components/links/secondaryLinkSmall.vue";

defineProps<{
  paginated: PaginatedStudentListData;
}>();

const pages: BreadcrumbItem[] = [
  { name: "Student", href: route("students.index"), current: route().current("students.index") },
];
</script>

<template>
  <Head title="Students Page" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Students Page</BaseHeader>

  <BasePage>
    <Card>
      <CardHeader>Head</CardHeader>

      <BaseTable>
        <BaseTHead>
          <BaseTH
            mobile
            position="left"
            >NAME
          </BaseTH>

          <BaseTH>REGISTRATION NUMBER</BaseTH>

          <BaseTH>GENDER</BaseTH>

          <BaseTH>STATUS</BaseTH>

          <BaseTH>DEPARTMENT</BaseTH>

          <BaseTH mobile>ACTION</BaseTH>
        </BaseTHead>

        <BaseTBody>
          <BaseTR
            v-for="student in paginated.data"
            :key="student.id">
            <BaseTD
              mobile
              position="left">
              {{ student.name }}

              <div class="mt-1 flex flex-col text-gray-700 sm:block lg:hidden dark:text-gray-300">
                <span>{{ student.registrationNumber }}</span>

                <span class="hidden sm:inline"> || </span>

                <span>
                  <Badge :color="student.statuColor">{{ student.status }}</Badge>
                </span>

                <span class="hidden sm:inline"> || </span>

                <span>{{ student.department }}</span>
              </div>
            </BaseTD>

            <BaseTD>{{ student.registrationNumber }}</BaseTD>

            <BaseTD>{{ student.gender }}</BaseTD>

            <BaseTD>
              <Badge :color="student.statuColor">{{ student.status }}</Badge>
            </BaseTD>

            <BaseTD>{{ student.department }}</BaseTD>

            <BaseTD mobile>
              <SecondaryLinkSmall :href="route('students.show', { student: student })">View</SecondaryLinkSmall>
            </BaseTD>
          </BaseTR>
        </BaseTBody>
      </BaseTable>

      <CardFooter>
        <Pagination :paginated="paginated" />
      </CardFooter>
    </Card>
  </BasePage>
</template>
