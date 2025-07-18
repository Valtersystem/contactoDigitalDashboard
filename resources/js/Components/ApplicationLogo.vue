<script setup lang="ts">

const props = defineProps<{
    imageName?: string
}>();

</script>

<template>
  <img class="" :src="`/images/${props.imageName}`" alt="">
</template>
