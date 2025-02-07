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
import { computed } from "vue";
import EmptyState from "@/components/emptyState.vue";
import BaseTable from "@/components/tables/baseTable.vue";
import BaseTHead from "@/components/tables/baseTHead.vue";
import BaseTH from "@/components/tables/baseTH.vue";
import BaseTBody from "@/components/tables/baseTBody.vue";
import BaseTD from "@/components/tables/baseTD.vue";
import BaseTR from "@/components/tables/baseTR.vue";
import Badge from "@/components/badge.vue";
import SecondaryLinkSmall from "@/components/links/secondaryLinkSmall.vue";

const props = defineProps<{
  data: App.Data.Imports.ExcelImportEventListData;
}>();

const form = useForm({ file: null });

const submit = () => {
  form.post(route("import.final-results.store"));
};

const pages: BreadcrumbItem[] = [
  {
    name: "Final Result Import",
    href: route("import.final-results.index"),
    current: route().current("import.final-results.index"),
  },
];

const hasEvent = computed(() => props.data.events.length > 0);
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
                type="file"
                @input="form.file = $event.target.files[0]" />

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
        <BaseTable>
          <BaseTHead>
            <BaseTH position="left">FileName</BaseTH>

            <BaseTH position="left">Status</BaseTH>

            <BaseTH>Actions</BaseTH>
          </BaseTHead>

          <BaseTBody>
            <BaseTR
              v-for="event in data.events"
              :key="event.id">
              <BaseTD position="left">{{ event.fileName }}</BaseTD>

              <BaseTD position="left">
                <Badge :color="event.statusColor">{{ event.status }}</Badge>
              </BaseTD>

              <BaseTD>
                <SecondaryLinkSmall
                  :href="route('import.final-results.delete', { event: event.id })"
                  method="post">
                  Delete
                </SecondaryLinkSmall>
              </BaseTD>
            </BaseTR>
          </BaseTBody>
        </BaseTable>
      </template>

      <template v-else>
        <EmptyState
          description="Start by selecting a file to upload and click Upload"
          title="No Final Result Upload Found" />
      </template>
    </BaseSection>
  </BasePage>
</template>
