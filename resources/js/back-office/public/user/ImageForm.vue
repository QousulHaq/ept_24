<template>
  <div class="form-group">
    <label for="image">Image</label>
    <input type="hidden" name="image_id" :value="image">
    <input id="image" type="file" name="image" class="form-control" @change="uploadFile">
  </div>
</template>

<script>
  export default {
    name: "AudioUpload",
    props: {
      image: {
        required: false,
        type: String
      }
    },
    methods: {
      uploadFile (e) {
        let form = document.querySelector('input[name=image_id]');

        this.deleteFile(form);

        let formData = new FormData();
        formData.append('file', e.target.files[0]);

        this.request('api.back-office.attachment', {}, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          data: formData
        }).then((response) => {
          form.value = response.data.id;
        })
      },
      deleteFile (form) {
        if (form.value) {
          this.request('api.back-office.attachment.destroy', {
            attachment_uuid: form.value
          })
        }
      }
    }
  }
</script>

<style scoped>

</style>
