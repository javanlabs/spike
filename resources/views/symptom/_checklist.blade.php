<?php
    $i = 0;
    $container = [];
?>

@foreach(explode("\n", $checklist) as $line)
    <?php $line = trim($line); ?>
    <?php if(trim($line) == '') {$i=0;echo "</br>";continue;} ?>
    @if($i++ == 0)
        <h5>{{ $line }}</h5>
        <?php $container[0] = $line; ?>
    @else
        @if($line[0] == '#')
            @if(strlen($line) == 1)
                <br/>
            @else
                <h6><strong>{{ substr($line, 1) }}</strong></h6>
            @endif
        @else
            <label>
                <input type="checkbox" name="{{ snake_case($container[0]) . "[" . $line . "]" }}" value="1"/>
                <span class="label-body">{{ $line }}</span>
            </label>
        @endif
    @endif
@endforeach
