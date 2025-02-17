<script lang="ts" setup>
import { Head, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/components/breadcrumb.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseSection from "@/layouts/main/partials/baseSection.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import FormGroup from "@/components/forms/formGroup.vue";
import BaseFormSection from "@/components/forms/baseFormSection.vue";
import InputError from "@/components/inputs/inputError.vue";
import EmptyState from "@/components/emptyState.vue";
import { computed, ref, watch, onMounted, toRef } from "vue";
import { BreadcrumbItem } from "@/types";
import { usePage } from "@inertiajs/vue3";
import UploadedExcelList from "@/pages/programCurriculum/import/partials/uploadedExcelList.vue";

const props = defineProps<{
  data: App.Data.Imports.ExcelImportEventListData;
}>();

const pages: BreadcrumbItem[] = [
  {
    name: "Final Result Import",
    href: route("import.final-results.index"),
    current: route().current("import.final-results.index"),
  },
];

const user = usePage().props.user;

const importEventListData = toRef(props.data, "events");

const form = useForm({ file: null as File | null });

const hasEvent = computed(() => props.data.events.length > 0);

const hasUnfinishedImport = computed(() =>
  props.data.events.some((importEvent) => importEvent.status !== "completed" && importEvent.status !== "failed"),
);

onMounted(() => {
  if (hasUnfinishedImport.value) {
    subscribe();
  }
});

watch(hasUnfinishedImport, () => {
  if (hasUnfinishedImport.value) {
    subscribe();
  } else {
    unsubscribe();
  }
});

const onFileChange = () => {
  if (fileInput.value?.files?.length) {
    form.file = fileInput.value.files[0];
  }
};

const submit = () => {
  form.post(route("import.final-results.store"));
};

const fileInput = ref<HTMLInputElement | null>(null);

const subscribe = () => {
  window.Echo.channel(`excelImports.${user.id}`).listen("ExcelImportStatusChanged", (event: any) => {
    const index = importEventListData.value.findIndex((importEvent) => importEvent.id === event.importEventData.id);

    if (index > -1) {
      importEventListData.value[index].status = event.importEventData.status;
      importEventListData.value[index].statusColor = event.importEventData.statusColor;
    }
  });
};

const unsubscribe = () => {
  window.Echo.leaveChannel(`excelImports.${user.id}`);
};
</script>

<template>
  <Head title="Import Final Results" />

  <Breadcrumb :pages="pages" />

  <BaseHeader>Import Final (Reconciled) Results</BaseHeader>

  <BasePage>
    <BaseSection>
      <BaseFormSection
        description="Select the Reconciled Results Excel (.xlsx) File and click Upload"
        header="Upload Reconciled Results">
        <form
          class="mt-6 space-y-6"
          @submit.prevent="submit">
          <FormGroup>
            <div class="flex-1">
              <InputLabel
                for="file"
                value="Excel File" />

              <input
                ref="fileInput"
                type="file"
                @change="onFileChange" />

              <progress
                v-if="form.progress"
                :value="form.progress.percentage"
                max="100">
                {{ form.progress.percentage }}%
              </progress>

              <InputError :message="form.errors.file" />
            </div>

            <div>
              <PrimaryButton :disabled="form.processing">Upload</PrimaryButton>
            </div>
          </FormGroup>
        </form>
      </BaseFormSection>
    </BaseSection>

    <BaseSection>
      <template v-if="hasEvent">
        <UploadedExcelList :data="data" />
      </template>

      <template v-else>
        <EmptyState
          description="Start by selecting a file to upload and click Upload"
          title="No Final Result Upload Found" />
      </template>
    </BaseSection>
  </BasePage>
</template>
