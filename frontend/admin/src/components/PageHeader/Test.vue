<template>
  <li class="dropdown notifications-menu">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" @click="currentUrl = 'x'">
    <i class="fa fa-comments"></i>
  </a>
  <ul class="dropdown-menu">
    <li class="header text-center">Une remarque ?</li>
    <li>
        <div class="container-fluid margin-bottom">
        <form method="POST" accept-charset="UTF-8" role="form" action="/ticket/comment">
          <div class="form-group">
            <textarea rows="4" id="comment" placeholder="Faites-nous part de vos idées !" name="comment"class="form-control"></textarea>
          </div>
          <input type="submit" id="submit" value="Proposer" name="submit" class="btn btn-primary btn-block margin-bottom">
        </form>
        </div>
    </li>
  </ul>
</li>
</template>

<script>
export default {
  name: 'test',
  data () {
    return {
      comment: ''
    }
  }
}
</script>

<style>
</style>
