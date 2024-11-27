<template>
  <div class="card" v-show="classifications.length > 0">
    <div class="card-body">
      <div class="form-group">
        <label for="category">Category</label>
        <select id="category" class="form-control" @input="e => $emit('input', e.target.value)">
          <option :key="`option-${i}`" v-for="(classification, i) in classifications" :value="classification.hash"
                  :selected="value === classification.hash">{{classification.name}}</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "SelectClassification",
    props: {
      classifications: {
        type: Array,
        default: () => ([])
      },
      value: {
        type: String,
        default: undefined
      }
    }
  }
</script>

<style scoped>

</style>
