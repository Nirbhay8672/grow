<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Permission {
    id: number;
    name: string;
    guard_name: string;
}

interface Role {
    id: number;
    name: string;
    guard_name: string;
    permissions: Permission[];
    created_at: string;
    updated_at: string;
}

interface Props {
    form: any;
    permissions: Permission[];
    editingRole: Role | null;
    errors: Record<string, string[]>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    submit: [];
    cancel: [];
}>();

const groupedPermissions = computed(() => {
    const groups: Record<string, Permission[]> = {};
    
    props.permissions.forEach(permission => {
        const [resource] = permission.name.split('.');
        if (!groups[resource]) {
            groups[resource] = [];
        }
        groups[resource].push(permission);
    });
    
    return groups;
});

const isPermissionChecked = (permissionName: string) => {
    return props.form.permissions.includes(permissionName);
};

const togglePermission = (permissionName: string) => {
    const permissions = [...props.form.permissions];
    const index = permissions.indexOf(permissionName);
    
    if (index > -1) {
        permissions.splice(index, 1);
    } else {
        permissions.push(permissionName);
    }
    
    props.form.permissions = permissions;
};

const selectAllForResource = (resource: string) => {
    const resourcePermissions = groupedPermissions.value[resource];
    const allSelected = resourcePermissions.every(p => isPermissionChecked(p.name));
    
    if (allSelected) {
        // Deselect all
        resourcePermissions.forEach(p => {
            const index = props.form.permissions.indexOf(p.name);
            if (index > -1) {
                props.form.permissions.splice(index, 1);
            }
        });
    } else {
        // Select all
        resourcePermissions.forEach(p => {
            if (!props.form.permissions.includes(p.name)) {
                props.form.permissions.push(p.name);
            }
        });
    }
};

const isResourceFullySelected = (resource: string) => {
    const resourcePermissions = groupedPermissions.value[resource];
    return resourcePermissions.every(p => isPermissionChecked(p.name));
};

const isResourcePartiallySelected = (resource: string) => {
    const resourcePermissions = groupedPermissions.value[resource];
    const selectedCount = resourcePermissions.filter(p => isPermissionChecked(p.name)).length;
    return selectedCount > 0 && selectedCount < resourcePermissions.length;
};
</script>

<template>
    <div>
        <!-- Role Name -->
        <div class="form-group mb-2">
            <label for="name" class="form-label mb-1" style="font-size: 14px;">
                Role Name
            </label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control form-control-sm"
                :class="{ 'is-invalid': errors.name }"
                placeholder="Enter role name"
                required
            />
            <div v-if="errors.name" class="invalid-feedback" style="font-size: 12px;">
                {{ errors.name[0] }}
            </div>
        </div>

        <!-- Permissions -->
        <div class="form-group mb-0">
            <label class="form-label mb-1" style="font-size: 14px;">
                Permissions
            </label>
            
            <div class="row">
                <div
                    v-for="(resourcePermissions, resource) in groupedPermissions"
                    :key="resource"
                    class="col-md-6 mb-3"
                >
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-capitalize">
                                {{ resource }} Management
                            </h6>
                            <button
                                type="button"
                                @click="selectAllForResource(resource)"
                                class="btn btn-sm btn-outline-primary"
                                style="line-height: 2rem;"
                            >
                                {{ isResourceFullySelected(resource) ? 'Deselect All' : 'Select All' }}
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div
                                    v-for="permission in resourcePermissions"
                                    :key="permission.id"
                                    class="col-6 mb-2"
                                >
                                    <div class="form-check">
                                        <input
                                            :id="`permission-${permission.id}`"
                                            type="checkbox"
                                            :checked="isPermissionChecked(permission.name)"
                                            @change="togglePermission(permission.name)"
                                            class="form-check-input"
                                            style="width: 16px; height: 16px;"
                                        />
                                        <label
                                            :for="`permission-${permission.id}`"
                                            class="form-check-label ms-2"
                                            style="font-size: 14px;"
                                        >
                                            {{ permission.name.split('.')[1] }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

