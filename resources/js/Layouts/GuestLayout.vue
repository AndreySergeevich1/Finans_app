<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-b from-purple-100 to-white p-6">


    <div class="mb-8">
      <Link href="/">
        <ApplicationLogo class="w-20 h-20" />
      </Link>
    </div>


    <div class="w-full sm:max-w-md bg-white rounded-3xl shadow-xl p-8 space-y-6">
      <slot />
    </div>

  </div>
</template>
