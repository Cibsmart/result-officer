<script lang="ts" setup>
import EmptyState from '@/components/emptyState.vue';
import { IconLink } from '@/components/links';
import { computed } from 'vue';
import { BaseTable, BaseTBody, BaseTD, BaseTH, BaseTHead, BaseTR } from '@/components/tables';

const props = defineProps<{
    students: App.Data.Cleared.ClearedStudentListData;
}>();

const hasRows = computed(() => props.students.data.length > 0);
</script>

<template>
    <div>
        <div
            class="mt-1 divide-y divide-solid divide-gray-300 ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:divide-gray-600 dark:ring-gray-600">
            <div class="grid grid-flow-col">
                <div class="p-2">
                    FACULTY: <span class="font-bold text-black dark:text-white">{{ students.faculty.name }}</span>
                </div>
            </div>

            <div class="grid grid-flow-col">
                <div class="p-2">
                    DEPARTMENT: <span class="font-bold text-black dark:text-white">{{ students.department.name }}</span>
                </div>
            </div>
        </div>

        <div>
            <template v-if="hasRows">
                <div class="-mx-4 mt-4 overflow-auto ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:ring-gray-600">
                    <BaseTable>
                        <BaseTHead>
                            <BaseTH> SN</BaseTH>

                            <BaseTH position="left"> NAME</BaseTH>

                            <BaseTH position="left"> REGISTRATION NUMBER</BaseTH>

                            <BaseTH> FCGPA</BaseTH>

                            <BaseTH position="left"> DATE CLEARED</BaseTH>

                            <!--              <BaseTH> ACTIONS</BaseTH>-->
                        </BaseTHead>

                        <BaseTBody>
                            <template
                                v-for="(student, index) in students.data"
                                :key="student.id">
                                <BaseTR>
                                    <BaseTD>{{ index + 1 }}</BaseTD>

                                    <BaseTD
                                        mobile
                                        position="left"
                                        >{{ student.name }}
                                    </BaseTD>

                                    <BaseTD position="left"> {{ student.registrationNumber }}</BaseTD>

                                    <BaseTD mobile> {{ student.fcgpa }}</BaseTD>

                                    <BaseTD position="left">{{ student.batch }}</BaseTD>

                                    <!--                  <BaseTD>-->
                                    <!--                    <PrimaryButtonSmall> graduate</PrimaryButtonSmall>-->
                                    <!--                  </BaseTD>-->
                                </BaseTR>
                            </template>
                        </BaseTBody>
                    </BaseTable>
                </div>
            </template>

            <EmptyState
                v-else
                description="Get started by clearing students that are ready for graduation"
                title="No Cleared Student Found in the selected year and month">
                <IconLink :href="route('graduand.index', { department: students.department.slug })"
                    >Vet Students
                </IconLink>
            </EmptyState>
        </div>
    </div>
</template>
