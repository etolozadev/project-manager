<template>
  <AppLayout
    :title="`Editar — ${client.name}`"
    :breadcrumbs="[
      { label: 'Clientes', href: route('clients.index') },
      { label: client.name, href: route('clients.show', client.id) },
      { label: 'Editar' }
    ]"
  >
    <div class="mx-auto max-w-2xl">
      <form @submit.prevent="submit" class="space-y-6">

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-5 text-sm font-semibold text-gray-900">Información del cliente</h2>

          <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de cliente <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
              <label v-for="opt in [{val:'company',lbl:'Empresa'},{val:'person',lbl:'Persona Natural'}]" :key="opt.val"
                     class="flex cursor-pointer items-center gap-2">
                <input type="radio" :value="opt.val" v-model="form.type"
                       class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"/>
                <span class="text-sm text-gray-700">{{ opt.lbl }}</span>
              </label>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre / Razón social <span class="text-red-500">*</span></label>
              <input type="text" v-model="form.name" :class="inputClass"/>
              <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600">{{ form.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">RUT <span class="text-red-500">*</span></label>
              <input type="text" v-model="form.rut" :class="inputClass"/>
              <p class="mt-1 text-xs text-gray-400">Formato: XX.XXX.XXX-Y</p>
              <p v-if="form.errors.rut" class="mt-1 text-xs text-red-600">{{ form.errors.rut }}</p>
            </div>
            <div v-if="form.type === 'company'">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Persona de contacto</label>
              <input type="text" v-model="form.contact_person" :class="inputClass"/>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
              <input type="email" v-model="form.email" :class="inputClass"/>
              <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">{{ form.errors.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
              <input type="text" v-model="form.phone" :class="inputClass"/>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-5 text-sm font-semibold text-gray-900">Dirección</h2>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección</label>
              <input type="text" v-model="form.address" :class="inputClass"/>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Ciudad</label>
              <input type="text" v-model="form.city" :class="inputClass"/>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Región</label>
              <select v-model="form.region" :class="inputClass">
                <option value="">Seleccionar región</option>
                <option v-for="r in regiones" :key="r" :value="r">{{ r }}</option>
              </select>
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Sitio web</label>
              <input type="url" v-model="form.website" :class="inputClass"/>
              <p v-if="form.errors.website" class="mt-1.5 text-xs text-red-600">{{ form.errors.website }}</p>
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas internas</label>
              <textarea v-model="form.notes" rows="3" :class="inputClass + ' resize-none'"/>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <label class="flex cursor-pointer items-center gap-3">
            <input type="checkbox" v-model="form.active"
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"/>
            <div>
              <p class="text-sm font-medium text-gray-900">Cliente activo</p>
              <p class="text-xs text-gray-500">Los inactivos no aparecen en selectores de proyectos.</p>
            </div>
          </label>
        </div>

        <div class="flex items-center justify-end gap-3">
          <Link :href="route('clients.show', client.id)"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Cancelar
          </Link>
          <button type="submit" :disabled="form.processing"
                  class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
            {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ client: Object });

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';

const regiones = [
  'Arica y Parinacota','Tarapacá','Antofagasta','Atacama','Coquimbo',
  'Valparaíso','Metropolitana',"O'Higgins",'Maule','Ñuble','Biobío',
  'La Araucanía','Los Ríos','Los Lagos','Aysén','Magallanes',
];

const form = useForm({
  type:            props.client.type,
  name:            props.client.name,
  rut:             props.client.rut,
  email:           props.client.email    ?? '',
  phone:           props.client.phone    ?? '',
  contact_person:  props.client.contact_person ?? '',
  address:         props.client.address  ?? '',
  city:            props.client.city     ?? '',
  region:          props.client.region   ?? '',
  website:         props.client.website  ?? '',
  notes:           props.client.notes    ?? '',
  active:          props.client.active,
});

function submit() {
  form.patch(route('clients.update', props.client.id));
}
</script>
