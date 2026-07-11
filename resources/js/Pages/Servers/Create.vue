<template>
  <AppLayout
    title="Nuevo servidor"
    :breadcrumbs="[{ label: 'Servidores', href: route('servers.index') }, { label: 'Nuevo servidor' }]"
  >
    <ServerForm
      :projects="projects"
      :preselected-project="preselectedProject"
      @submit="submit"
      :processing="form.processing"
      :errors="form.errors"
      :form="form"
    />
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ServerForm from './ServerForm.vue';

const props = defineProps({
  projects:           Array,
  preselectedProject: [String, Number],
});

const form = useForm({
  project_id: props.preselectedProject ?? '',
  name: '', type: 'vps', provider: '', ip_address: '', hostname: '',
  os: '', php_version: '', db_type: '', db_version: '',
  panel: 'none', web_server: '', ssh_user: 'root', ssh_port: 22,
  domain: '', url: '', status: 'active',
  hosting_expires_at: '', domain_expires_at: '', ssl_expires_at: '',
  cred_ssh_password: '', cred_db_user: '', cred_db_password: '',
  cred_db_name: '', cred_panel_url: '', cred_panel_user: '',
  cred_panel_password: '', cred_extra: '',
  notes: '',
});

function submit() {
  form.post(route('servers.store'));
}
</script>
