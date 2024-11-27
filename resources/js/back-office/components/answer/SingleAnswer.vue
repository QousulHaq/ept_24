<template>
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">
        <label>
          <input class="form-check-input" type="radio" :name="name" :value="$vnode.key" :checked="checked"
                 @change="e => $emit('selected', e.target.value)">
        </label>
      </span>
    </div>
    <input type="text" class="form-control" :value="value" @input="e => $emit('input', e.target.value)" :readonly="!editable">
    <div class="input-group-append">
      <button class="btn btn-outline-danger" type="button" v-if="closable" @click="() => $emit('closed', $vnode.key)">
        <i class="fa fa-times"/>
      </button>
    </div>
  </div>
</template>

<script>
  export default {
    name: "SingleAnswer",
    props: {
      name: {
        type: String,
        default: 'single'
      },
      value: {
        type: String
      },
      checked: false,
      closable: true,
      editable: true
    }
  }
</script>

<style scoped>
  .input-group input[type=radio] {
    transform: translate(0.8rem, -0.2rem);
  }
</style>
