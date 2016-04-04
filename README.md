# Diagram

READ ME
=======

Offers you the ability to create top-down tree diagrams from an array.

## LICENSING ##



This library is free software; you can redistribute it and/or modify it under
the terms of the GNU Lesser General Public License as published by the Free
Software Foundation; either version 2 of the License, or any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU Lesser General Public License (LICENSE file)
for more details.

You should have received a copy of the GNU Lesser General Public License along
with this library; if not, write to the Free Software Foundation, Inc., 51
Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

## USAGE ##
```
    $g = new \Diagram(
      array(
        'border_radius' => 4,
        'rect_bordercolor' => array(204,204,204),
        'rect_bgcolor' => array(255, 255, 255),
        'fontcolor' => array(56,108,187),
        'borderwidth' => 1
      )
    );

		$arr = array(
			'Task 1' => array(
				'Task 2' => array(
					'Task 3' => array('Task 4'),
					'Task 5'
				),
				'Task 6' => array(
					'Task 7' => array(
						'Task 8',
						'Task 9' =>  array(
							'Task 10',
							'Task 11'
						),
						'Task 12'
					)
				),
				'Task 13'
			)
		);

		$g->SetData($arr);
		$g->Draw();
```
## ACKNOWLEDGMENTS ##

Based on phpqrcode library
Copyright (C) 2010-2013 by Dominik Dzienia

Which was based on C libqrencode library (ver. 3.1.1) 
Copyright (C) 2006-2010 by Kentaro Fukuchi
http://megaui.net/fukuchi/works/qrencode/index.en.html

QR Code is registered trademarks of DENSO WAVE INCORPORATED in JAPAN and other
countries.

Reed-Solomon code encoder is written by Phil Karn, KA9Q.
Copyright (C) 2002, 2003, 2004, 2006 Phil Karn, KA9Q
 
