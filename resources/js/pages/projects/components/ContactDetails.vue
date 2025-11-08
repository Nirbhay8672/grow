<script setup lang="ts">
import { Plus, X } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Contact {
    id: number;
    name: string;
    mobile: string;
    email: string;
    designation: string;
}

interface Props {
    users: User[];
    contacts: Contact[];
    form: {
        restricted_user_ids: number[];
    };
    errors: Record<string, string[]>;
    validateForm: () => void;
    addContact: () => void;
    removeContact: (contactId: number) => void;
}

defineProps<Props>();
</script>

<template>
    <div class="mb-4">
        <h5 class="mb-3 section-title">Contact Details</h5>
        
        <!-- Dynamic Contact Fields -->
        <div 
            v-for="(contact, index) in contacts" 
            :key="contact.id" 
            class="row mb-3"
        >
            <div class="col-md-3">
                <div class="form-group">
                    <label :for="`contact_name_${contact.id}`" class="form-label">
                        Name <span class="text-danger">*</span>
                    </label>
                    <input
                        :id="`contact_name_${contact.id}`"
                        v-model="contact.name"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors[`contacts.${index}.name`] }"
                        placeholder="Enter name"
                        @blur="validateForm"
                        required
                    />
                    <div v-if="errors[`contacts.${index}.name`]" class="invalid-feedback">
                        {{ errors[`contacts.${index}.name`][0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label :for="`contact_mobile_${contact.id}`" class="form-label">
                        Mobile <span class="text-danger">*</span>
                    </label>
                    <input
                        :id="`contact_mobile_${contact.id}`"
                        v-model="contact.mobile"
                        type="tel"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors[`contacts.${index}.mobile`] }"
                        placeholder="Enter mobile number"
                        @blur="validateForm"
                        @input="validateForm"
                        maxlength="10"
                        required
                    />
                    <div v-if="errors[`contacts.${index}.mobile`]" class="invalid-feedback">
                        {{ errors[`contacts.${index}.mobile`][0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label :for="`contact_email_${contact.id}`" class="form-label">Email</label>
                    <input
                        :id="`contact_email_${contact.id}`"
                        v-model="contact.email"
                        type="email"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors[`contacts.${index}.email`] }"
                        placeholder="Enter email address"
                        @blur="validateForm"
                    />
                    <div v-if="errors[`contacts.${index}.email`]" class="invalid-feedback">
                        {{ errors[`contacts.${index}.email`][0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label :for="`contact_designation_${contact.id}`" class="form-label">Designation</label>
                    <input
                        :id="`contact_designation_${contact.id}`"
                        v-model="contact.designation"
                        type="text"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors[`contacts.${index}.designation`] }"
                        placeholder="Enter designation"
                    />
                    <div v-if="errors[`contacts.${index}.designation`]" class="invalid-feedback">
                        {{ errors[`contacts.${index}.designation`][0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group mb-0">
                    <label class="form-label hidden-label">Action</label>
                    <div class="d-flex gap-1">
                        <button
                            v-if="index === 0"
                            type="button"
                            @click="addContact"
                            class="btn btn-primary btn-sm d-flex align-items-center justify-content-center contact-action-btn contact-add-btn"
                            title="Add Contact"
                        >
                            <Plus :size="18" color="white" class="contact-icon" />
                        </button>
                        <button
                            v-if="index > 0"
                            type="button"
                            @click="removeContact(contact.id)"
                            class="btn btn-danger btn-sm d-flex align-items-center justify-content-center contact-action-btn"
                            title="Remove Contact"
                        >
                            <X :size="18" color="white" class="contact-icon" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Restricted User Field -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="restricted_user_ids" class="form-label">Restricted Users</label>
                    <select
                        id="restricted_user_ids"
                        v-model="form.restricted_user_ids"
                        class="form-control form-control-sm"
                        :class="{ 'is-invalid': errors.restricted_user_ids }"
                        :multiple="true"
                    >
                        <option 
                            v-for="user in users" 
                            :key="user.id" 
                            :value="user.id"
                        >
                            {{ user.name }} ({{ user.email }})
                        </option>
                    </select>
                    <div v-if="errors.restricted_user_ids" class="invalid-feedback">
                        {{ errors.restricted_user_ids[0] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

