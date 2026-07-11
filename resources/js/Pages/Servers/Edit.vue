<template>
  <AppLayout
    :title="`Editar — ${server.name}`"
    :breadcrumbs="[
      { label: 'Servidores', href: route('servers.index') },
      { label: server.name, href: route('servers.show', server.id) },
      { label: 'Editar' }
    ]"
  >
    <ServerForm
      :projects="projects"
      :form="form"
      :errors="form.errors"
      :processing="form.processing"
      @submit="submit"
    />
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ServerForm from './ServerForm.vue';

const props = defineProps({
  server:   Object,
  projects: Array,
});

const form = useForm({
  project_id:         props.server.project_id,
  name:               props.server.name,
  type:               props.server.type,
  provider:           props.server.provider           ?? '',
  ip_address:         props.server.ip_address         ?? '',
  hostname:           props.server.hostname           ?? '',
  os:                 props.server.os                 ?? '',
  php_version:        props.server.php_version        ?? '',
  db_type:            props.server.db_type            ?? '',
  db_version:         props.server.db_version         ?? '',
  panel:              props.server.panel              ?? 'none',
  web_server:         props.server.web_server         ?? '',
  ssh_user:           props.server.ssh_user           ?? 'root',
  ssh_port:           props.server.ssh_port           ?? 22,
  domain:             props.server.domain             ?? '',
  url:                props.server.url                ?? '',
  status:             props.server.status,
  hosting_expires_at: props.server.hosting_expires_raw ?? '',
  domain_expires_at:  props.server.domain_expires_raw  ?? '',
  ssl_expires_at:     props.server.ssl_expires_raw     ?? '',
  // Credenciales existentes (descifradas)
  cred_ssh_password:   props.server.credentials?.ssh_password   ?? '',
  cred_db_user:        props.server.credentials?.db_user        ?? '',
  cred_db_password:    props.server.credentials?.db_password    ?? '',
  cred_db_name:        props.server.credentials?.db_name        ?? '',
  cred_panel_url:      props.server.credentials?.panel_url      ?? '',
  cred_panel_user:     props.server.credentials?.panel_user     ?? '',
  cred_panel_password: props.server.credentials?.panel_password ?? '',
  cred_extra:          props.server.credentials?.extra          ?? '',
  notes:               props.server.notes ?? '',
});

function submit() {
  form.patch(route('servers.update', props.server.id));
}
</script>
