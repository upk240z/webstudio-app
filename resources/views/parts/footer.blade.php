<footer>
    Copyright(c) 2019 webstudio.tokyo All rights reserved.
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js" integrity="sha384-IYwIhxhQzTKn3wg5sIMnuvvJOCJCQskrc3tsuwcrCxhe+xgvQhyfuxb1w0rNGMov" crossorigin="anonymous"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js" integrity="sha384-B1miHxuCmAMGc0405Cm/zSXCc38EP5hNOy2bblqF6yiX01Svinm1mWMwJDdNDBGr" crossorigin="anonymous"></script>
<script src="{{ asset('js/validation.js') }}"></script>
<script src="{{ asset('js/common.js') }}"></script>

<textarea id="copy-boad" name="copy-boad" style="display:none"></textarea>

{{ $slot }}
