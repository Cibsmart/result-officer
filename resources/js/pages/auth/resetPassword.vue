<script lang="ts" setup>
import InputError from "@/components/inputError.vue";
import InputLabel from "@/components/inputLabel.vue";
import PrimaryButton from "@/components/primaryButton.vue";
import TextInput from "@/components/textInput.vue";
import LayoutGuest from "@/layouts/guest/layoutGuest.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps<{
  email: string;
  token: string;
}>();

defineOptions({ layout: LayoutGuest });

const form = useForm({
  token: props.token,
  email: props.email,
  password: "",
  password_confirmation: "",
});

const submit = () => {
  form.post(route("password.store"), {
    onFinish: () => {
      form.reset("password", "password_confirmation");
    },
  });
};
</script>

<template>
  <Head title="Reset Password" />

  <form @submit.prevent="submit">
    <div>
      <InputLabel
        for="email"
        value="Email" />

      <TextInput
        id="email"
        v-model="form.email"
        autocomplete="username"
        autofocus
        class="mt-1 block w-full"
        required
        type="email" />

      <InputError
        :message="form.errors.email"
        class="mt-2" />
    </div>

    <div class="mt-4">
      <InputLabel
        for="password"
        value="Password" />

      <TextInput
        id="password"
        v-model="form.password"
        autocomplete="new-password"
        class="mt-1 block w-full"
        required
        type="password" />

      <InputError
        :message="form.errors.password"
        class="mt-2" />
    </div>

    <div class="mt-4">
      <InputLabel
        for="password_confirmation"
        value="Confirm Password" />

      <TextInput
        id="password_confirmation"
        v-model="form.password_confirmation"
        autocomplete="new-password"
        class="mt-1 block w-full"
        required
        type="password" />

      <InputError
        :message="form.errors.password_confirmation"
        class="mt-2" />
    </div>

    <div class="mt-4 flex items-center justify-end">
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing">
        Reset Password
      </PrimaryButton>
    </div>
  </form>
</template>
