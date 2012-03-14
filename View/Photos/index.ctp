<div class="photos">
	<?php 
		echo $this->Form->create(false, array('action' => 'index'));
		echo $this->Form->input('search', array('label' => 'Search'));
		echo $this->Form->end();
		?>
	<p class="clear">&nbsp;</p>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?></p>
	<?php 
	foreach($photos as $photo){
		$p = $photo['Photo'];
		$image = $this->Html->image($p['thumbnail'], ['alt' => $p['title']]);
		$link = $this->Html->link($image, $p['large'], ['target' => '_blank', 'escape' => false]);				
		echo $this->Html->div('photo thumbnail', $link);
	}
	?>
	<div class="paging">
	<?php echo $this->Paginator->prev('<< '.__('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next(__('next').' >>', array(), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->last();?>
	</div>
</div>