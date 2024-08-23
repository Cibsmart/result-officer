<script lang="ts" setup>
import InputError from "@/components/inputs/inputError.vue";
import InputLabel from "@/components/inputs/inputLabel.vue";
import PrimaryButton from "@/components/buttons/primaryButton.vue";
import TextInput from "@/components/inputs/textInput.vue";
import { useForm, usePage } from "@inertiajs/vue3";

defineProps<{
  status?: string;
}>();

const user = usePage().props.user;

const form = useForm({
  name: user.name,
  email: user.email,
});
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Profile Information</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Update your account's profile information and email address.
      </p>
    </header>

    <form
      class="mt-6 space-y-6"
      @submit.prevent="form.patch(route('profile.update'))">
      <div>
        <InputLabel
          for="name"
          value="Name" />

        <TextInput
          id="name"
          v-model="form.name"
          autocomplete="name"
          autofocus
          class="mt-1 block w-full"
          required
          type="text" />

        <InputError
          :message="form.errors.name"
          class="mt-2" />
      </div>

      <div>
        <InputLabel
          for="email"
          value="Email" />

        <TextInput
          id="email"
          v-model="form.email"
          autocomplete="username"
          class="mt-1 block w-full"
          required
          type="email" />

        <InputError
          :message="form.errors.email"
          class="mt-2" />
      </div>

      <div class="flex items-center gap-4">
        <PrimaryButton :disabled="form.processing"> Save</PrimaryButton>

        <Transition
          enter-active-class="transition ease-in-out"
          enter-from-class="opacity-0"
          leave-active-class="transition ease-in-out"
          leave-to-class="opacity-0">
          <p
            v-if="form.recentlySuccessful"
            class="text-sm text-gray-600 dark:text-gray-400">
            Saved.
          </p>
        </Transition>
      </div>
    </form>
  </section>
</template>
