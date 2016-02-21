<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_sitelist($customer_id, $data, $job_sites)
{   
    $html =  '<h4>' . $data['title'] . '</h4>';

    if ( ! empty($job_sites) ) 
    {
        foreach ($job_sites as $site)
        {
            //print_r($site); exit;
            $html .= "<h5 class='content-heading-row'><a href='/sites/edit/" . $site['id'] . "'>" . $site['address'] . "</a><h5>";
            $html .= '<div class="sitelist_view"><i class="togglecont fa fa-chevron-down pull-right"></i>';
            if ( ! empty($site['estimates']) ) 
            {
                $estimates = $site['estimates'];

                foreach ($estimates as $estimate)
                {
                    if (isset($estimate->job_site_id))
                    {
                        $job_site_id   = $estimate->job_site_id;

                        $estimate->keys = array( 'customer'   => array( 'th' => 'Customer' ),
                                                 //'job_site'   => array( 'th' => 'Job Site',   'a' => "/sites/view/$job_site_id" ),
                                                 'windows'    => array( 'th' => 'Windows'    ),
                                                 'cost'       => array( 'th' => 'Est. Cost'  ),
                                                 'created'    => array( 'th' => 'Created'    ),
                                                 'created_by' => array( 'th' => 'Created By' ),
                                                 'dealer'     => array( 'th' => 'Dealer'     )); 
                    }
                }

                $plural = count($estimates) == '1' ? '' : 's';
                $html .=  "<h5>" . count($estimates) . " Estimate" . $plural . "</h5>";
                $html .= '<table class="sitelist_table">';
                $html .= '<tr class="sitelist_row">';
                $html .= '<th><span class="sitelist_cell">Actions</span></th>';

                $estimate = $estimates[0];
                if (isset($estimate->job_site_id))
                {
                    foreach ($estimate->keys as $key => $value)
                    {
                        if ( isset($value) && ! empty($value) )
                        {
                            $html .= '<th>' . $value['th'] . '</th>';
                        }
                    }
                }

                $html .= "</tr>";

                foreach ($estimates as $estimate)
                {
                    //prd($estimate);
                    if (isset($estimate->job_site_id))
                    {
                        $html .= '<tr class="sitelist_row">';
                        $html .= '<td>';
                        $html .= '<a href="/estimates/edit/' . $estimate->id . '" class="sitelist_cell icon"><i class="sprite-icons view"></i></a>';
                        $html .= '</td>';

                        foreach ($estimate->keys as $key => $value)
                        {
                            $html .= '<td>';
                            if ( isset($value) && ! empty($value) )
                            {
                                if ( isset($value['a']))
                                {
                                    $html .= '<a href="' . $value['a'] . '" class="sitelist_cell">' . $estimate->$key . '</a>';
                                }
                                else
                                {
                                    $html .= $estimate->$key;
                                }

                            } else {

                                $html .= $estimate->$key;
                            }
                            $html .= '</td>';
                        }
                    }
                }
                $html .='       </tr> 
                            </table>
                    ';
            } else {
                $html .= '<h5>Estimates</h5>';
                $html .= '<table class="sitelist_table"><tbody><tr><td><div class="alert alert-grey" role="alert">There are no associated Estimates.</div></td></tr></tbody></table>';
            }

            if ( ! empty($site['quotes']) ) 
            {
                $quotes = $site['quotes'];

                foreach ($quotes as $quote)
                {
                    if (isset($quote->job_site_id))
                    {
                        $job_site_id   = $quote->job_site_id;

                        $quote->keys = array( 'customer'   => array( 'th' => 'Customer'),
                                              //'job_site'   => array( 'th' => 'Job Site',   'a' => "/sites/view/$job_site_id" ),
                                              'windows'    => array( 'th' => 'Windows',   ),
                                              'created'    => array( 'th' => 'Created',   ),
                                              'created_by' => array( 'th' => 'Created By',),
                                              'dealer'     => array( 'th' => 'Dealer',    )); 
                    }
                }

                $plural = count($quotes) == '1' ? '' : 's';
                $html .=  "<h5>" . count($quotes) . " Quote" . $plural . "</h5>";
                $html .= '<table class="sitelist_table">';
                $html .= '<tr class="sitelist_row">';
                $html .= '<th><span class="sitelist_cell">Actions</span></th>';

                $quote = $quotes[0];
                if (isset($quote->job_site_id))
                {
                    foreach ($quote->keys as $key => $value)
                    {
                        if ( isset($value) && ! empty($value) )
                        {
                            $html .= '<th>' . $value['th'] . '</th>';
                        }
                    }
                }

                $html .= "</tr>";

                foreach ($quotes as $quote)
                {
                    if (isset($quote->job_site_id))
                    {
                        $html .= '<tr class="sitelist_row">';
                        $html .= '<td>';
                        $html .= '<a href="/quotes/edit/' . $quote->id . '" class="sitelist_cell icon"><i class="sprite-icons view"></i></a>';
                        $html .= '</td>';

                        foreach ($quote->keys as $key => $value)
                        {
                            $html .= '<td>';
                            if ( isset($value) && ! empty($value) )
                            {
                                if ( isset($value['a'] ) )
                                {
                                    $html .= '<a href="' . $value['a'] . '" class="sitelist_cell">' . $quote->$key . '</a>';
                                }
                                else
                                {
                                    $html .= $quote->$key;
                                }

                            } else {

                                $html .= $quote->$key;
                            }
                            $html .= '</td>';
                        }
                    }
                }
                $html .='       </tr> 
                            </table>
                    ';

            } else {
                $html .= '<h5>Quotes</h5>';
                $html .= '<table class="sitelist_table"><tbody><tr><td><div class="alert alert-grey" role="alert">There are no associated Quotes.</div></td></tr></tbody></table>';
            }

            if ( ! empty($orders) ) 
            {
                $orders = $site['orders'];

                foreach ($orders as $order)
                {
                    if (isset($order->job_site_id))
                    {
                        $job_site_id = $order->job_site_id;
                        $created_by  = $order->created_by_id;

                        $quote->keys = array( 'customer'   => array( 'th' => 'Customer',   'a' => "/customers/view/$customer_id" ),
                                              'job_site'   => array( 'th' => 'Job Site',   'a' => "/sites/view/$job_site_id" ),
                                              'windows'    => array( 'th' => 'Windows',    ),
                                              'cost'       => array( 'th' => 'Cost',       ),
                                              'created'    => array( 'th' => 'Created',    ),
                                              'created_by' => array( 'th' => 'Created By', ),
                                              'dealer'     => array( 'th' => 'Dealer',     )); 
                    }
                }

                $plural = $orders['count'] === '1' ? '' : 's';
                $html .= "<h5>" . $orders['count'] . " Order" . $plural . "</h5>";
                $html .= '<table class="sitelist_table">';
                $html .= '<tr class="sitelist_row">';
                $html .= '<th><span class="sitelist_cell">Actions</span></th>';

                $order = $orders[0];
                if (isset($order->job_site_id))
                {
                    foreach ($order->keys as $key => $value)
                    {
                        if ( isset($value) && ! empty($value) )
                        {
                            $html .= '<th>' . $value['th'] . '</th>';
                        }
                    }
                }

                $html .= "</tr>";

                foreach ($orders as $order)
                {
                    if (isset($order->job_site_id))
                    {
                        $html .= '<tr class="sitelist_row">';
                        $html .= '<td>';
                        $html .= '<a href="" class="sitelist_cell"><i class="fa fa-times"></i></a>';
                        $html .= '</td>';

                        foreach ($order->keys as $key => $value)
                        {
                            $html .= '<td>';
                            if ( isset($value) && ! empty($value) )
                            {
                                if ( isset($value['a'] ) )
                                {
                                    $html .= '<a href="' . $value['a'] . '" class="sitelist_cell">' . $order->$key . '</a>';
                                }
                                else
                                {
                                    $html .= $order->$key;
                                }

                            } else {

                                $html .= $order->$key;
                            }
                            $html .= '</td>';
                        }
                    }
                }
                $html .='       </tr> 
                            </table>
                    ';

            }

            $html .= '</div>';
        }

    } else {

        $html .= '<br/>';
        $html .= '<div class="alert alert-warning">';
        $html .= $data['sites_empty_message'];
        $html .= '</div>';
    }

    return $html;
}   

?>
