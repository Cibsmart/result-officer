<script lang="ts" setup>
import DeleteUserForm from "@/pages/profile/partials/deleteUserForm.vue";
import UpdatePasswordForm from "@/pages/profile/partials/updatePasswordForm.vue";
import UpdateProfileInformationForm from "@/pages/profile/partials/updateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";

defineProps<{
  status?: string;
}>();
</script>

<template>
  <Head title="Profile" />

  <BaseHeader>Profile</BaseHeader>

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
