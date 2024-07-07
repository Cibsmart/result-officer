<script lang="ts" setup>
import PrimaryButton from "@/components/primaryButton.vue";
import TextInput from "@/components/textInput.vue";
import InputLabel from "@/components/inputLabel.vue";
import InputError from "@/components/inputError.vue";
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Result Information</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Input student's matriculation number to view results</p>
    </header>

    <form
      class="mt-6 space-y-6"
      @submit.prevent="form.patch(route('profile.update'))"></form>
  </section>
</template>
