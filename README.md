# rev_api
rev.com api in PHP

## Examples

```
//Start the Rev api client
$rev = new Rev('client api key', 'user api key');

//upload a video
$input = $rev->uploadVideoUrl(self::MEDIA_URL);

//Create an order
$order = new CaptionOrderSubmission($rev);

//Add the video to the order
$order->addInput($input);

//Set some order details
$order->setClientRef('example reference number');
$order->setComment('example comment');
$order->setNotification('http://example.org/test.php', CaptionOrderSubmission::NOTIFICATION_LEVEL_DETAILED);
$order->setPriority(CaptionOrderSubmission::PRIORITY_TIME_INSENSITIVE);
$order->setOutputFormats(array('WebVtt', 'SubRip'));

//Send the order
$order_number = $order->send();

//get the order
$order = $rev->getOrder($order_number);

//display the order status
$order->getStatus();

//get order attachments
foreach ($completed_order->getAttachments() as $attachment) {
    if (!$attachment->isMedia()) {
        //Only get attachments that rev.com has completed (captions)
        
        //Display the content as its default content type
        echo $attachment->getContent();
        
        //Display the content as a different content type
        echo $attachment->getContent('.txt');
    }
}

//Cancel the order
$rev->cancelOrder($order_number);

//Get the first page of orders
$orders = $rev->getOrders();
foreach ($orders as $order) {
  echo $order->getOrderNumber();
}

//Get the rest of the pages
while ($orders = $orders->getNextPage()) {
    foreach ($orders as $order) {
      echo $order->getOrderNumber();
    }
}

```



## Implementation Progress:

- [ ] POST /inputs
- [x] -- via URL
- [ ] -- via upload
- [ ] -- via upload w/ multipart requests
- [x] POST /orders (Transcription)
- [x] POST /orders (Caption)
- [x] POST /orders (Translation)
- [x] GET /orders/{order_num}
- [x] GET /orders
- [x] -- by page
- [ ] -- by list of IDs
- [x] GET /orders/{order_num}/cancel
- [x] GET /attachments/{id}
- [x] GET /attachments/{id}/content
