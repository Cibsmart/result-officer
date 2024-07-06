<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import LayoutMain from "@/layouts/main/layoutMain.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import UpdatePasswordForm from "@/pages/profile/partials/updatePasswordForm.vue";
import DeleteUserForm from "@/pages/profile/partials/deleteUserForm.vue";
import UpdateProfileInformationForm from "@/pages/profile/partials/updateProfileInformationForm.vue";

defineOptions({ layout: LayoutMain });
</script>

<template>
  <Head title="View Student Result" />

  <BaseHeader> View Student Results</BaseHeader>

  <BasePage>
    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
          <UpdateProfileInformationForm
            :status="status"
            class="max-w-xl" />
        </div>

        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
          <UpdatePasswordForm class="max-w-xl" />
        </div>

        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
          <DeleteUserForm class="max-w-xl" />
        </div>
      </div>
    </div>
  </BasePage>
</template>
