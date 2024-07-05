<script lang="ts" setup>
import InputError from "@/components/inputError.vue";
import InputLabel from "@/components/inputLabel.vue";
import PrimaryButton from "@/components/primaryButton.vue";
import TextInput from "@/components/textInput.vue";
import LayoutGuest from "@/layouts/guest/layoutGuest.vue";
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
  password: "",
});

const submit = () => {
  form.post(route("password.confirm"), {
    onFinish: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <LayoutGuest>
    <Head title="Confirm Password" />

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
      This is a secure area of the application. Please confirm your password before continuing.
    </div>

    <form @submit.prevent="submit">
      <div>
        <InputLabel
          for="password"
          value="Password" />

        <TextInput
          id="password"
          v-model="form.password"
          autocomplete="current-password"
          autofocus
          class="mt-1 block w-full"
          required
          type="password" />

        <InputError
          :message="form.errors.password"
          class="mt-2" />
      </div>

      <div class="mt-4 flex justify-end">
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          class="ms-4">
          Confirm
        </PrimaryButton>
      </div>
    </form>
  </LayoutGuest>
</template>
