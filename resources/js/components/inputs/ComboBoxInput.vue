<script lang="ts" setup>
import { cn } from '@/lib/utils';
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
} from '@/components/ui/combobox';
import { Button } from '@/components/ui/button';
import { Check, ChevronsUpDown, Search } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        items: Array<DropdownData>;
        placeholder: string;
        empty: string;
    }>(),
    {
        placeholder: 'Select ...',
        empty: 'No Item Found.',
    },
);

import DropdownData = App.Data.Dropdown.DropdownData;

const selected = defineModel<DropdownData>();
</script>

<template>
    <Combobox
        v-model="selected"
        by="label">
        <ComboboxAnchor as-child>
            <ComboboxTrigger as-child>
                <Button
                    class="justify-between"
                    variant="outline">
                    {{ selected?.label ?? 'Select' }}

                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </ComboboxTrigger>
        </ComboboxAnchor>

        <ComboboxList>
            <div class="relative w-full max-w-sm items-center">
                <ComboboxInput
                    :placeholder="placeholder"
                    class="h-10 rounded-none border-0 border-b pl-9 focus-visible:ring-0" />

                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                    <Search class="text-muted-foreground size-4" />
                </span>
            </div>

            <ComboboxEmpty> {{ empty }}</ComboboxEmpty>

            <ComboboxGroup>
                <ComboboxItem
                    v-for="item in items"
                    :key="item.value"
                    :value="item">
                    {{ item.label }}

                    <ComboboxItemIndicator>
                        <Check :class="cn('ml-auto h-4 w-4')" />
                    </ComboboxItemIndicator>
                </ComboboxItem>
            </ComboboxGroup>
        </ComboboxList>
    </Combobox>
</template>
