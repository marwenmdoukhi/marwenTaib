declare acts_array = (258 260 266 269 270 275 277 279 281 283 286 287 289 290 292 297 298 299 300 301 303 305 310 311 312 313 315 316 317 318 319 321 323 325 326 327 328 329 331 332 333 334 335 338 341 342 344 345 346 348 350 356 358 359 361 365 367 370 373 378 381 391 393 394 396 397 399 402 403 404 405 406 407 408 410 414 416 417 418 419 427 430 433 434 435 438 440 441 442 443 444 447 448 449 450 451 452 453 454 455 457 458 459 460 464 465 466 468 469 470 474 479 480 485 488 490 491 494 495 496 498 499 500 501 502 504 505 506 508 510 513 516 517 518 519 522 524 525 526 527 528 530 534 535 537 538 544 547 548 553 556 557 559 567 568 571 572 573 574 576 578 579 580 581 582 584 586 587 590 592 596 597 598 599 600)
for i in "${acts_array[@]}";do
AWS_ACCESS_KEY_ID=00811667c9ffe0bfa887 AWS_SECRET_ACCESS_KEY=uUsPsUNbAtGZ41rVwgLHVG+nK901lE8ZpkVBvplh aws --endpoint-url=https://s3-rct.cnb-prive.net s3 sync /data/acte_sous_signature_privee/src/assets/documents/$i/ s3://rct-bkt-encours-assp/$i/document/ --no-verify-ssl;
echo "folder $i uploaded to s3 "
done;