<script lang="ts" setup>
import PrimaryButton from "@/components/primaryButton.vue";
import TextInput from "@/components/textInput.vue";
import InputLabel from "@/components/inputLabel.vue";
import InputError from "@/components/inputError.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
  registration_number: "",
});

const submit = () => {
  form.post(route("results.view"));
};
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Result Information</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Input student's registration number to view results</p>
    </header>

    <form
      class="mt-6 space-y-6"
      @submit.prevent="submit">
      <div>
        <InputLabel
          for="registration_number"
          value="Registration Number" />

        <TextInput
          id="registration_number"
          v-model="form.registration_number"
          autocomplete="registration_number"
          autofocus
          class="mt-1 block w-full"
          placeholder="EBSU/2009/51486"
          required
          type="text" />

        <InputError
          :message="form.errors.registration_number"
          class="mt-2" />
      </div>

      <div>
        <PrimaryButton :disabled="form.processing">View</PrimaryButton>
      </div>
    </form>
  </section>
</template>
