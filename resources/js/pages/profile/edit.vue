<script lang="ts" setup>
import DeleteUserForm from "@/pages/profile/partials/deleteUserForm.vue";
import UpdatePasswordForm from "@/pages/profile/partials/updatePasswordForm.vue";
import UpdateProfileInformationForm from "@/pages/profile/partials/updateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";
import BaseHeader from "@/layouts/main/partials/baseHeader.vue";
import BasePage from "@/layouts/main/partials/basePage.vue";
import BaseSection from "@/components/baseSection.vue";

defineProps<{
  status?: string;
}>();
</script>

<template>
  <Head title="Profile" />

  <BaseHeader>Profile</BaseHeader>

  <BasePage>
    <BaseSection>
      <UpdateProfileInformationForm
        :status="status"
        class="max-w-xl" />
    </BaseSection>

    <BaseSection>
      <UpdatePasswordForm class="max-w-xl" />
    </BaseSection>

    <BaseSection>
      <DeleteUserForm class="max-w-xl" />
    </BaseSection>
  </BasePage>
</template>
